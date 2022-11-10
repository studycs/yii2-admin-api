<?php

namespace app\handlers;

class CorsFilter extends \yii\filters\Cors
{
    public $cors = [
        'Origin' => ['http://localhost:5173','http://localhost:3000','https://wp.lxknet.cn'],
        'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
        'Access-Control-Request-Headers' => ['*'],
        'Access-Control-Allow-Origin' => ['http://localhost:5173','http://localhost:3000','https://wp.lxknet.cn'],
        'Access-Control-Allow-Credentials' => true,
        'Access-Control-Max-Age' => 86400,
        'Access-Control-Expose-Headers' => [],
    ];
}