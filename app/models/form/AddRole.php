<?php

namespace app\models\form;

use app\models\entity\AuthItem;

class AddRole extends \yii\base\Model
{
    public $roleCode;
    public $roleName;

    public function rules()
    {
        return [
            [['roleCode','roleName'],'required']
        ];
    }

    public function validateName($attribute){
        $roleInfo = AuthItem::findOne(['name'=>$this->roleCode,'type'=>1]);
        if($roleInfo !=null) $this->addError($attribute, '角色名称已被使用.');
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function create(){
        if($this->validate()){
            $model = new AuthItem();
            $model->type = 1;
            $model->name = $this->roleCode;
            $model->description = $this->roleName;
            $model->created_at = time();
            $model->updated_at = time();
            if(!$model->save()) throw new \Exception(current($model->firstErrors) ?? '角色创建失败！');
            return true;
        }
        return false;
    }

}