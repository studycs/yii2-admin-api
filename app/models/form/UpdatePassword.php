<?php
namespace app\models\form;
use app\models\entity\User;

class UpdatePassword extends \yii\base\Model
{
    public $oldPassword;
    public $password;
    public $password2;

    public function rules()
    {
        return [
            [['oldPassword','password','password2'],'required'],
            [['oldPassword','password','password2'],'string'],
            ['oldPassword','validateOldPassword'],
            ['password','validatePassword']
        ];
    }

    public function validateOldPassword($attribute){
        if (!$this->hasErrors()){
            $user = User::findOne(\yii::$app->user->getId());
            if (!$user || !$user->validatePassword($this->oldPassword)) {
                $this->addError($attribute, '原密码错误.');
            }
        }
    }

    public function validatePassword($attribute){
        if (!$this->hasErrors()){
            if($this->password != $this->password2){
                $this->addError($attribute, '确认密码与密码不一致.');
            }
        }
    }

    public function update(){
        if ($this->validate()){
            $model = User::findOne(\yii::$app->user->getId());
            $model->setPassword($this->password);
            $model->updated_at = time();
            if(!$model->save()) throw new \Exception(current($model->firstErrors)??'修改密码失败.');
            return true;
        }
        return false;
    }
}