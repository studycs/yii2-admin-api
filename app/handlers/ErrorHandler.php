<?php
namespace app\handlers;
use yii\web\Response;

/**
 * Error Handler
 */
class ErrorHandler extends \yii\web\ErrorHandler
{
    public $errorAction = 'site/error';

    public function renderException($exception)
    {
        if (\Yii::$app->has('response')) {
            $response = \Yii::$app->getResponse();
            $response->isSent = false;
            $response->stream = null;
            $response->data = null;
            $response->content = null;
        } else {
            $response = new Response();
        }
        $result = \Yii::$app->runAction($this->errorAction);
        if ($result instanceof Response) {
            $response = $result;
        } else {
            $response->data = $result;
        }
        $response->send();
    }

}