<?php
namespace app\handlers;
use yii\web\Response;

class Controller extends \yii\rest\Controller
{
    /**
     * @param $message
     * @param $code
     * @return Response
     */
    public function error($message,$code=1){
        return $this->asJson([
            'code'=>$code,
            'message'=>$message
        ]);
    }

    /**
     * @param $message
     * @param $data
     * @return Response
     */
    public function success($message,$data=[]){
        return $this->asJson([
            'code'=>0,
            'message'=>$message,
            'data'=>$data
        ]);
    }

    public function behaviors()
    {
        return [
            ['class'=>\app\handlers\CorsFilter::class],
            ['class'=>\app\handlers\ContentFilter::class],
            ['class'=>\app\handlers\HttpHeaderAuth::class],
        ];
    }

}