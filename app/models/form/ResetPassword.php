<?php

namespace app\models\form;

use app\models\entity\User;

class ResetPassword extends \yii\base\Model
{
    public $userId;
    public $password;

    public function rules()
    {
        return [
            [['userId','password'],'required']
        ];
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function reset(){
        if ($this->validate()) {
            $model = User::findOne($this->userId);
            $model->setPassword($this->password);
            $model->updated_at = time();
            if(!$model->save()) throw new \Exception('密码重置失败.');
            return true;
        }
        return false;
    }

}