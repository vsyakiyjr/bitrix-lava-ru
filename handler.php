<?php

namespace Sale\Handlers\PaySystem;

use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Request;
use Bitrix\Main\Type\DateTime;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\Payment;

Loc::loadMessages(__FILE__);

class youp_business_lavaHandler extends PaySystem\ServiceHandler {

    protected $youp_lang = 'YOUP_BUSINESS_LAVA_';
    protected static $request = [];
	 
    /**
     * @param Payment $payment
     * @param Request|null $request
     * @return PaySystem\ServiceResult
     */
    public function initiatePay(Payment $payment, Request $request = null) {
        if (!($invoice = $payment->getField('PS_INVOICE_ID'))) {
            $invoice = $this->createInvoice($payment);
            $this->saveInvoice($payment->getField('ORDER_ID'), $invoice);
        } 
        $payment_url = "https://pay.lava.ru/invoice/$invoice";
        $this->setExtraParams(compact('payment_url'));
        return $this->showTemplate($payment, "template");
    }

    /**
     *  
     */
    public function createInvoice(Payment $payment) { 
        $data = $this->prepareCurlData($payment);		 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.lava.ru/business/invoice/create");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false); 
		$response = json_decode(curl_exec($ch), true);
		curl_close($ch);
        return $response['status'] == '200' ? $response['data']['id'] : "";
    }

    /**
     *  
     */
    public function prepareCurlData($payment) {
        $hook = $this->getBusinessValue($payment, 'Hook_url');
        $delimiter = (strpos($hook, '?') !== false ? '&' : '?');
        $order_id = $payment->getField('ORDER_ID');
        $payment_id = $payment->getId();	
		$price = $this->convertToDefault($payment->getSum()); 			 
		$data = [
			'sum' => $price,
			'orderId' => $order_id, 
			'shopId' => $this->getBusinessValue($payment, 'Shop_id'),
			'hookUrl' => $hook . $delimiter . 'youp_business_lava',
			'successUrl' => $this->getBusinessValue($payment, 'Success_url'),
			'failUrl' => $this->getBusinessValue($payment, 'Fail_url'), 
			'customFields' => json_encode(compact('payment_id')),
			'comment' =>  ucfirst(SITE_SERVER_NAME) . ': '
            . Loc::getMessage("{$this->youp_lang}ORDER_PAYMENT")
            . $order_id,
		];		
		$data = $this->signature($data, $this->getBusinessValue($payment, 'Secret_key')); 		
		return $data;
    }

    /**
     *  
     */
    public function saveInvoice($orderID, $invoice) {
        $order = \Bitrix\Sale\Order::load($orderID);
        foreach ($order->getPaymentCollection() as $payment) {
            $payment->setField('PS_INVOICE_ID', $invoice);
        }
        $order->save();
    }




    /**
     * @return array
     */
    public static function getIndicativeFields() {
        return array('youp_business_lava');
    }

    /**
     * @param Request $request
     * @param $paySystemId
     * @return bool
     */
    static protected function isMyResponseExtended(Request $request, $paySystemId) {
        $arRequest = json_decode(file_get_contents('php://input'), true);
        $fields = json_decode(
                htmlspecialchars_decode($arRequest['custom_fields']), true
        );
        if (gettype($fields) == 'array' && isset($fields['payment_id'])) {
            static::$request = array_merge($arRequest, $fields);
            return true;
        }
        return false;
    }
 
    /**
     * @param Payment $payment
     * @param Request $request
     * @return PaySystem\ServiceResult
     */
    public function processRequest(Payment $payment, Request $request) {
		 
		$result = []; 
		$data = file_get_contents('php://input');
			 
            if ($this->isCorrectSum($payment, $data['amount'])) {
				 	
				$dateInsert = date('d.m.Y H:i:s');  
				$date = new \Bitrix\Main\Type\DateTime($dateInsert);  

				$order = \Bitrix\Sale\Order::load($data["order_id"]); 
				$payments = $order->getPaymentCollection();  
				$payments[0]->setPaid("Y"); 
				$order->setField("DATE_UPDATE", $date); 
				$order->setField("STATUS_ID", "P");  
				$order->save(); 
		
			}
			 
        return $result;
    }




    /**
     *  
     */
    protected function signature($data, $secretKey)
    { 
        ksort($data);
        $signature = hash_hmac("sha256", json_encode($data), $secretKey);
        $data['signature'] = $signature;
        return $data;
    }
	
    /**
     *  
     */
    protected function isCorrectSum($payment, $amount) {
        return ($this->convertToDefault($amount) >= $payment->getSum());
    }

    /**
     * @return array
     */
    public function getCurrencyList() {
        return array('RUB');
    } 
	
	/**
 	* @return array
 	*/
    private function convertToDefault($price) 
	{   
		return round(\CCurrencyRates::ConvertCurrency($price, 'USD', 'RUB'), 2);
	}
	
	/**
 	* @return array
 	*/
    private function convertToUSD($price) 
	{   
		return round(\CCurrencyRates::ConvertCurrency($price, 'RUB', 'USD'), 2);
	}
}