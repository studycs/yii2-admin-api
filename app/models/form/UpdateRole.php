<?php

namespace app\models\form;

use app\models\entity\AuthAssignment;
use app\models\entity\AuthItem;
use app\models\entity\AuthItemChild;

class UpdateRole extends \yii\base\Model
{
    public $roleId;
    public $roleCode;
    public $roleName;

    public function rules()
    {
        return [
            [['roleId','roleName','roleCode'],'required']
        ];
    }

    public function validateName($attribute){
        if($this->roleId != $this->roleCode){
            $roleItem = AuthItem::findOne(['name'=>$this->roleCode,'type'=>1]);
            if($roleItem !=null) $this->addError($attribute, '角色名称已被使用.');
        }
    }

    public function update(){
        if($this->validate()){
            $trans = \yii::$app->db->beginTransaction();
            try{
                $model = AuthItem::findOne(['name'=>$this->roleId]);
                $model->name = $this->roleCode;
                $model->description = $this->roleName;
                $model->updated_at = time();
                if(!$model->save()) throw new \Exception(current($model->firstErrors) ?? '角色更新失败！');
                AuthAssignment::updateAll(['item_name'=>$this->roleCode],['item_name'=>$this->roleId]);
                AuthItemChild::updateAll(['parent'=>$this->roleCode],['parent'=>$this->roleId]);
                $trans->commit();
                return true;
            }catch (\Throwable $e){
                $trans->rollBack();
                throw new \Exception($e->getMessage());
            }
        }
        return false;
    }


}