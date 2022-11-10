<?php


namespace app\components;


class Request extends \yii\web\Request
{
    public $cookieValidationKey = 'hOGmEgkIyjIFlZuucFxXjjVnInLwBdVMhdK2qoiGnM';

    public $parsers = [
        'application/json'=>\yii\web\JsonParser::class
    ];

}