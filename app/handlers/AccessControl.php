<?php
namespace app\handlers;
/**
 * Class AccessControl
 * @package app\behaviors
 */
class AccessControl extends \yii\filters\AccessControl
{
    public $rules = [
        ['actions' => ['logout'],'allow'=>true,'roles'=>['@']]
    ];

    public $ruleConfig = ['class' => 'app\handlers\AccessRule'];
}