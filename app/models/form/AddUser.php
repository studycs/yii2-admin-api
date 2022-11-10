<?php

namespace app\models\form;

use app\models\entity\AuthAssignment;
use app\models\entity\User;

class AddUser extends \yii\base\Model
{
    public string $username;
    public string $nickname;
    public string $password;
    public array  $roles;
    public string $email;

    public function rules()
    {
        return [
            [['username','nickname','password','roles'],'required'],
            ['username','validateUsername'],
            ['email','email'],
        ];
    }

    public function validateUsername($attribute){
        $userinfo = User::findOne(['username'=>$this->username,'status'=>User::STATUS_ACTIVE]);
        if(!empty($userinfo)) $this->addError($attribute, '用户名已被使用.');
    }

    /**
     * @return boolean
     * @throws \Exception
     */
    public function create(){
        if($this->validate()){
            $transaction = \yii::$app->db->beginTransaction();
            try{
                $user = new User();
                $user->generateAuthKey();
                $user->created_at = time();
                $user->updated_at = time();
                $user->email = $this->email;
                $user->username = $this->username;
                $user->nickname = $this->nickname;
                $user->setPassword($this->password);
                $user->status = User::STATUS_ACTIVE;
                $user->generatePasswordResetToken();
                if(!$user->save()) throw new \Exception('用户信息写入失败！');
                foreach ($this->roles as $role){
                    $model = new AuthAssignment();
                    $model->user_id = (string)$user->id;
                    $model->item_name = $role['roleId'];
                    $model->created_at = time();
                    if(!$model->save()) {
                        throw new \Exception('用户角色信息写入失败！');
                    }
                }
                $transaction->commit();
                return true;
            }catch(\Exception $e){
                $transaction->rollBack();
                throw new \Exception($e->getMessage());
            }
        }
        return false;
    }

}