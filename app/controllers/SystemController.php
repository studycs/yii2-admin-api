<?php
namespace app\controllers;
use app\models\entity\Menu;
use app\models\entity\User;
use app\models\form\AddMenu;
use app\models\form\AddRole;
use app\models\form\AddUser;
use app\models\form\ResetPassword;
use app\models\form\UpdatePassword;
use app\models\form\UserForm;
use app\models\form\UpdateUser;
use app\models\form\UpdateRole;
use app\models\form\UpdateMenu;
use app\models\entity\AuthItem;
use app\models\entity\AuthItemChild;
use app\models\entity\AuthAssignment;

class SystemController extends \app\handlers\Controller
{
    public function actionUser(){
        return $this->success('success',UserForm::getUsers());
    }

    public function actionAddUser(){
        try{
            $model = new AddUser();
            $model->load(\yii::$app->request->post(),'');
            $create = $model->create();
            if(!$create) return $this->error(current($model->firstErrors) ?? '参数错误！');
            return $this->success('用户创建成功！');
        }catch(\Throwable $e){
            return $this->error($e->getMessage());
        }
    }

    public function actionUpdateUser(){
        try{
            $model = new UpdateUser();
            $model->load(\yii::$app->request->post(),'');
            $create = $model->update();
            if(!$create) return $this->error(current($model->firstErrors) ?? '参数错误！');
            return $this->success('用户更新成功！');
        }catch(\Throwable $e){
            return $this->error($e->getMessage());
        }
    }

    public function actionDelUser(){
        try{
            $id = \yii::$app->request->get('id');
            $model = User::findOne(['id'=>$id]);
            $model->status = User::STATUS_DELETED;
            $model->save();
            AuthAssignment::deleteAll(['user_id'=>$id]);
            return $this->success('用户删除成功！');
        }catch(\Throwable $e){
            return $this->success('用户删除失败！');
        }
    }

    public function actionRole(){
        return $this->success('success',UserForm::getRoles());
    }

    public function actionAddRole(){
        try{
            $model = new AddRole();
            $model->load(\yii::$app->request->post(),'');
            $create = $model->create();
            if(!$create) return $this->error(current($model->firstErrors)??'角色创建失败！');
            return $this->success('角色创建成功！');
        }catch(\Throwable $e){
            return $this->error($e->getMessage());
        }
    }

    public function actionUpdateRole(){
        try{
            $model = new UpdateRole();
            $model->load(\yii::$app->request->post(),'');
            $create = $model->update();
            if(!$create) return $this->error(current($model->firstErrors)??'角色更新失败！');
            return $this->success('角色更新成功！');
        }catch(\Throwable $e){
            return $this->error($e->getMessage());
        }
    }

    public function actionDelRole(){
        $id = \yii::$app->request->get('id');
        $assign = AuthAssignment::findOne(['item_name'=>$id]);
        if($assign !=null){
            return $this->error('角色下有用户,不允许删除！');
        }else{
            AuthItem::deleteAll(['name'=>$id]);
            return $this->success('角色删除成功！');
        }
    }

    public function actionMenu(){
        return $this->success('success',UserForm::getMenus());
    }

    public function actionAddMenu(){
        try{
            $model = new AddMenu();
            $model->load(\yii::$app->request->post(),'');
            $create = $model->create();
            if(!$create) return $this->error(current($model->firstErrors)??'菜单添加失败！');
            return $this->success('菜单添加成功！');
        }catch(\Throwable $e){
            return $this->error($e->getMessage());
        }
    }

    public function actionUpdateMenu(){
        try{
            $model = new UpdateMenu();
            $model->load(\yii::$app->request->post(),'');
            $create = $model->create();
            if(!$create) return $this->error(current($model->firstErrors)??'菜单更新失败！');
            return $this->success('菜单更新成功！');
        }catch(\Throwable $e){
            return $this->error($e->getMessage());
        }
    }

    public function actionDelMenu(){
        $id = \yii::$app->request->get('id');
        if($id !=null){
            $menu = Menu::findOne(['id'=>$id]);
            if($menu !=null){
                Menu::deleteAll(['id'=>$id]);
                AuthItemChild::deleteAll(['child'=>$menu->route]);
                AuthItem::deleteAll(['name'=>$menu->route]);
            }
        }
        return $this->success('菜单删除成功！');
    }

    public function actionRoleMenu(){
        $id = \yii::$app->request->get('id');
        $data = \yii::$app->request->post();
        try{
            if(!empty($data)){
                AuthItemChild::deleteAll(['parent'=>$id]);
                $data = Menu::find()->select(['route'])->where(['id'=>$data])->column();
                $auth = AuthItem::find()->select(['name'])->where(['name'=>$data])->column();
                $role = \yii::$app->authManager->getRole($id);
                foreach ($auth as $value){
                    $item = \yii::$app->authManager->createPermission($value);
                    \yii::$app->authManager->addChild($role,$item);
                }
            }else{
                AuthItemChild::deleteAll(['parent'=>$id]);
            }
            return $this->success('权限分配成功！');
        }catch(\Throwable $e){
            return $this->success('权限分配失败！');
        }
    }

    public function actionPassword(){
        try{
            $model = new UpdatePassword();
            $model->load(\yii::$app->request->post(),'');
            if(!$model->update()) throw new \Exception(current($model->firstErrors)??'修改密码失败.');
            return $this->success('修改密码成功！');
        }catch(\Throwable $e){
            return $this->error($e->getMessage());
        }
    }

    public function actionResetPassword(){
        try{
            $model = new ResetPassword();
            $model->load(\yii::$app->request->post(),'');
            if(!$model->reset()) throw new \Exception(current($model->firstErrors)??'密码重置失败.');
            return $this->success('密码重置成功！');
        }catch(\Throwable $e){
            return $this->error($e->getMessage());
        }
    }
}