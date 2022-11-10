<?php
namespace app\controllers;
use app\models\entity\GetMessage;
use app\models\entity\MsgGroup;
use app\models\entity\ReceiveGroup;
use app\models\entity\TextNow;
use app\models\entity\TextnowGroup;

class DictionaryController extends \yii\rest\Controller
{
    public function error($message,$code=1){
        return $this->asJson(['code'=>$code,'message'=>$message]);
    }

    public function success($message,$data=[]){
        return $this->asJson(['code'=>0,'message'=>$message,'data'=>$data]);
    }

    public function behaviors()
    {
        return [
            ['class'=>\app\handlers\CorsFilter::class],
            ['class'=>\app\handlers\ContentFilter::class],
            ['class'=>\app\handlers\HttpPublicAuth::class],
        ];
    }

    public function actionIndex(){
        return $this->success('success');
    }

    public function actionTextnowGroups(){
        $isAdmin = \yii::$app->user->isAdmin;
        $query = TextnowGroup::find();
        if(!$isAdmin) $query->andFilterWhere(['admin_id'=>\yii::$app->user->getId()]);
        return $this->success('success',$query->all());
    }

    public function actionUserTextnowGroups(){
        $items = [];
        $query = TextnowGroup::find()->andFilterWhere(['admin_id'=>\yii::$app->user->ids])->all();
        return $this->success('success',$query);
    }

    public function actionUserReceiveGroups(){
        $query = ReceiveGroup::find()->andFilterWhere(['admin_id'=>\yii::$app->user->ids]);
        return $this->success('success',$query->all());
    }

    public function actionUserMessageGroups(){
        $query = MsgGroup::find()->andFilterWhere(['admin_id'=>\yii::$app->user->ids]);
        return $this->success('success',$query->all());
    }

    public function actionTextnowAll(){
        $Ids = GetMessage::find()->select(['textnow_id'])->column();
        $query = TextNow::find()->select(['id','num','status'])->where(['del_status'=>0,'id'=>$Ids])->all();
        return $this->success('success',$query);
    }

}