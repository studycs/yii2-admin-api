<?php

namespace app\models\form;

use app\models\entity\AuthAssignment;
use app\models\entity\User;

class UpdateUser extends \yii\base\Model
{
    public int $userId;
    public string $username;
    public string $nickname;
    public array  $roles;
    public string $email;

    public function rules()
    {
        return [
            [['userId','username','nickname','roles'],'required'],
            ['username','validateUsername'],
            ['email','email'],
        ];
    }

    public function validateUsername($attribute){
        $userinfo = User::findOne(['id'=>$this->userId,'status'=>User::STATUS_ACTIVE]);
        if($this->username != $userinfo->username){
            $userItem = User::findOne(['username'=>$this->username,'status'=>User::STATUS_ACTIVE]);
            if(!empty($userItem)) $this->addError($attribute, '用户名已被使用.');
        }elseif($userinfo==null){
            $this->addError($attribute, '编辑的用户不存在.');
        }
    }

    /**
     * @return boolean
     * @throws \Exception
     */
    public function update(){
        if($this->validate()){
            $transaction = \yii::$app->db->beginTransaction();
            try{
                $user = User::findOne(['id'=>$this->userId]);
                $user->updated_at = time();
                $user->email = $this->email;
                $user->username = $this->username;
                $user->nickname = $this->nickname;
                $user->status = User::STATUS_ACTIVE;
                if(!$user->save()) throw new \Exception('用户信息更新失败！');
                AuthAssignment::deleteAll(['user_id'=>$this->userId]);
                foreach ($this->roles as $role){
                    $model = new AuthAssignment();
                    $model->user_id = (string)$user->id;
                    $model->item_name = $role['roleId'];
                    $model->created_at = time();
                    if(!$model->save()) throw new \Exception('用户角色信息写入失败！');
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