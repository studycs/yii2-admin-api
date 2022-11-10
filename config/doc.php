<?php
use app\components\User;

class Yii extends \yii\BaseYii
{
    /**
     * @var Application the application instance
     */
    public static $app;
}

/**
 * @property User $user
 */
class Application extends \yii\web\Application {

}