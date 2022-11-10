<?php
$config = [
    'id' => 'basic',
    'language'=>'zh-CN',
    'sourceLanguage'=>'en-US',
    'basePath' => __DIR__.'/../app',
    'runtimePath'=>__DIR__.'/../runtime',
    'bootstrap' => ['log'],
    'aliases' => [
        '@root'   =>  dirname(__DIR__),
        '@npm'    => '@vendor/npm-asset',
        '@bower'  => '@vendor/bower-asset'
    ],
    'components'=> [
        'user'=>[
            'class'=>\app\components\User::class
        ],
        'cache'=>[
            'class'=>\yii\redis\Cache::class
        ],
        'db'=>[
            'class'=>\app\components\Connection::class
        ],
        'log'=>[
            'class'=>\app\components\Dispatcher::class
        ],
        'request'=>[
            'class'=>\app\components\Request::class
        ],
        'redis'=>[
            'class'=>\yii\redis\Connection::class,
//            'hostname' => '118.195.254.74',
//            'password' => 'admin2016'
        ],
        'urlManager'=>[
            'class'=>\app\components\UrlManager::class
        ],
        'authManager'=>[
            'class'=>\app\components\AuthManager::class
        ],
        'errorHandler'=>[
            'class'=>\app\handlers\ErrorHandler::class
        ],
    ],
    'params' => $_ENV,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => \yii\gii\Module::class,
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
