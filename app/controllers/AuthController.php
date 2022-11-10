<?php
namespace app\controllers;
use app\models\entity\Menu;
use app\models\entity\User;
use app\models\form\UserForm;

class AuthController extends \app\handlers\Controller
{
    public function actionUser(){
        return $this->success('success',UserForm::getUser());
    }

    public function actionUserinfo(){
        $id = \yii::$app->request->get('id');
        $field = \yii::$app->request->get('field');
        $value = \yii::$app->request->get('value');
        if($id ==null && $field=='username' && $value!=null){
            if($user = User::findOne(['username'=>$value,'status'=>User::STATUS_ACTIVE])){
                $data = [];
                $data['userId'] = $user->id;
                $data['email'] = $user->email;
                $data['username'] = $user->username;
                $data['nickname'] = $user->nickname;
                return $this->success('success',$data);
            }
        }elseif ($id != null && $field=='username' && $value!=null){
            $userinfo = User::findOne(['id'=>$id,'status'=>User::STATUS_ACTIVE]);
            if($value != $userinfo->username){
                if($userItem = User::findOne(['username'=>$value,'status'=>User::STATUS_ACTIVE])){
                    $data = [];
                    $data['userId'] = $userItem->id;
                    $data['email'] = $userItem->email;
                    $data['username'] = $userItem->username;
                    $data['nickname'] = $userItem->nickname;
                    return $this->success('success',$data);
                }
            }
        }
        return $this->error('账号不存在');
    }

    public function actionRoleMenu(){
        $data = [];
        $menus = Menu::find()->all();
        $id = \yii::$app->request->get('id');
        $items = \yii::$app->authManager->getPermissionsByRole($id);
        foreach ($menus as $menu){
            $temp = [];
            $temp['icon'] = $menu->icon;
            $temp['menuId'] = $menu->id;
            $temp['authority'] = $menu->route;
            $temp['parentId'] = $menu->parent ?? 0;
            $temp['sortNumber'] = $menu->order ?? 1;
            $temp['hide'] = $menu->status == 1 ? 1 : 0;
            $temp['title'] = $menu->name ?? 'Undefined';
            $temp['menuType'] = $menu->status == 2 ? 1 : 0;
            $temp['checked'] = isset($items[$menu->route]);
            $temp['path'] = ($menu->status==0||$menu->status==1) ? $menu->route : null ;
            $temp['component'] = ($menu->status==0||$menu->status==1) ? $menu->route : null ;
            $data[] = $temp;
        }
        return $this->success('success',$data);
    }
}