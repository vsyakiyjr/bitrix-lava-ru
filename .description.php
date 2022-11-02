<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
$youp_lang = "YOUP_BUSINESS_LAVA_"; 
$data = array(
    'NAME' => Loc::getMessage("{$youp_lang}NAME"),
    'SORT' => 500,
    'CODES' => array(
        'Shop_id' => array(
            "NAME" => Loc::getMessage("{$youp_lang}SHOP_ID"),
            'DESCRIPTION' => Loc::getMessage("{$youp_lang}DESC_SHOP_ID"),
            'SORT' => 200,
            'GROUP' => Loc::getMessage("{$youp_lang}ORDER_INFO"),
        ), 
        'Secret_key' => array(
            "NAME" => Loc::getMessage("{$youp_lang}SECRET_KEY"),
            'DESCRIPTION' => Loc::getMessage("{$youp_lang}DESC_SECRET_KEY"),
            'SORT' => 300,
            'GROUP' => Loc::getMessage("{$youp_lang}ORDER_INFO"),
        ), 
        'Secret_key_2' => array(
            "NAME" => Loc::getMessage("{$youp_lang}SECRET_KEY_2"),
            'DESCRIPTION' => Loc::getMessage("{$youp_lang}DESC_SECRET_KEY"),
            'SORT' => 300,
            'GROUP' => Loc::getMessage("{$youp_lang}ORDER_INFO"),
        ),   
        'Hook_url' => array(
            "NAME" => Loc::getMessage("{$youp_lang}HOOK_URL"),
            'DESCRIPTION' => Loc::getMessage("{$youp_lang}DESC_HOOK_URL"),
            'SORT' => 400,
            'GROUP' => Loc::getMessage("{$youp_lang}ORDER_INFO"),
        ),
        'Success_url' => array(
            "NAME" => Loc::getMessage("{$youp_lang}SUCCESS_URL"),
            'DESCRIPTION' => Loc::getMessage("{$youp_lang}DESC_SUCCESS_URL"),
            'SORT' => 500,
            'GROUP' => Loc::getMessage("{$youp_lang}ORDER_INFO"),
        ),
        'Fail_url' => array(
            "NAME" => Loc::getMessage("{$youp_lang}FAIL_URL"),
            'DESCRIPTION' => Loc::getMessage("{$youp_lang}DESC_FAIL_URL"),
            'SORT' => 600,
            'GROUP' => Loc::getMessage("{$youp_lang}ORDER_INFO"),
        ),
    )
);
