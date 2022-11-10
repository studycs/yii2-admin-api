<?php

namespace app\handlers;

class ContentFilter extends \yii\filters\ContentNegotiator
{
    public $formats = [
        'application/json'=>\yii\web\Response::FORMAT_JSON,
        'text/html'=>\yii\web\Response::FORMAT_JSON
    ];

}