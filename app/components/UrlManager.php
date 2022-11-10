<?php
namespace app\components;


class UrlManager extends \yii\web\UrlManager
{
    /**
     * @var bool $enablePrettyUrl
     */
    public $enablePrettyUrl = true;
    /**
     * @var bool $showScriptName
     */
    public $showScriptName  = false;
    /**
     * @var string $suffix
     */
    //public $suffix = '.html';
    /**
     * @var array $rules
     */
    public $rules = [
        '<controller:\w+>/<id:\d+>'=>'<controller>/view',
        '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
        '<module:\w+>/<controller:\w+>/<action:\w+>'=>'<module>/<controller>/<action>'
    ];
}