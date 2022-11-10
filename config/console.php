<?php
$config = [
    'id' => 'basic-console',
    'basePath' => __DIR__.'/../app',
    'bootstrap' => ['log'],
    'runtimePath'=>__DIR__.'/../runtime',
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@root'   =>  dirname(__DIR__),
        '@bower'  => '@vendor/bower-asset',
        '@npm'    => '@vendor/npm-asset',
        '@app/web'=> '@root/web',
    ],
    'components' => [
        'cache' => ['class' => \yii\redis\Cache::class],
        'redis'=>['class'=>\yii\redis\Connection::class],
        'db'=>['class'=>\app\components\Connection::class],
        'log' => ['class'=>\app\components\Dispatcher::class],
        'authManager'=>['class'=>\app\components\AuthManager::class],
    ],
    'params' => $_ENV,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
