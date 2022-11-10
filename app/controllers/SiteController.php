<?php
namespace app\controllers;
use Yii;
use yii\web\Response;
use app\models\form\LoginForm;
use app\models\form\RegisterForm;
/**
 * Class SiteController
 * @package app\controllers
 */
class SiteController extends \app\handlers\Controller
{
    public $enableCsrfValidation = false;
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            ['class'=>\app\handlers\CorsFilter::class],
            ['class'=>\app\handlers\ContentFilter::class],
        ];
    }

    /**
     * @return Response
     */
    public function actionIndex() {
        return $this->success('success');
    }

    public function actionError(){
        $error = \yii::$app->errorHandler;
        $_code = \yii::$app->errorHandler->exception->getCode();
        $message = \yii::t('app',$error->exception->getMessage());
        return $this->error($message,$_code);
    }

    /**
     * @return Response
     */
    public function actionLogin()
    {
        try{
            $model = new LoginForm();
            $model->load(Yii::$app->request->post(),'');
            return $this->success('登录成功！',$model->login());
        }catch(\Throwable $e){
            return $this->error($e->getMessage());
        }
    }
}
