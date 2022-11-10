<?php
namespace app\models\form;

use app\models\entity\AuthAssignment;
use app\models\entity\AuthItem;
use app\models\entity\Menu;
use app\models\entity\User;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class UserForm extends \yii\base\Model
{
    public const avatar = 'https://cdn.eleadmin.com/20200610/avatar.jpg';

    public static function getUser(){
        $result = [];
        $userinfo = User::findOne(['id'=>\yii::$app->user->getId()]);
        $result['userId'] = $userinfo->getId();
        $result['username'] = $userinfo->username;
        $result['nickname'] = $userinfo->nickname;
        $result['avatar'] = self::avatar;
        $auth = \yii::$app->authManager->getPermissionsByUser(\yii::$app->user->getId());
        $menus = Menu::find()->alias('a')->joinWith(['parent0'])->where(['a.status'=>[0,1,2],'a.route'=>array_keys($auth)])->all();
        $_menu = [];
        $authorities = [];
        foreach($menus as $menu){
            if(isset($auth[$menu->route])){
                $temp = [];
                $temp['icon'] = $menu->icon;
                $temp['menuId'] = $menu->id;
                $temp['authority'] = $menu->route;
                $temp['parentId'] = $menu->parent ?? 0;
                $temp['sortNumber'] = $menu->order ?? 1;
                $temp['hide'] = $menu->status == 1 ? 1 : 0;
                $temp['title'] = $menu->name ?? 'Undefined';
                $temp['menuType'] = $menu->status == 2 ? 1 : 0;
                $temp['path'] = ($menu->status==0||$menu->status==1) ? $menu->route : null ;
                $temp['component'] = ($menu->status==0||$menu->status==1) ? $menu->route : null ;
                $authorities[$menu->id] = $temp;
                if($menu->parent !=null){
                    $_temp = [];
                    $_temp['icon'] = $menu->parent0->icon;
                    $_temp['menuId'] = $menu->parent0->id;
                    $_temp['authority'] = $menu->parent0->route;
                    $_temp['parentId'] = $menu->parent0->parent ?? 0;
                    $_temp['sortNumber'] = $menu->parent0->order ?? 1;
                    $_temp['hide'] = $menu->parent0->status == 1 ? 1 : 0;
                    $_temp['title'] = $menu->parent0->name ?? 'Undefined';
                    $_temp['menuType'] = $menu->parent0->status == 2 ? 1 : 0;
                    $_temp['path'] = ($menu->parent0->status==0||$menu->parent0->status==1) ? $menu->parent0->route : null ;
                    $_temp['component'] = ($menu->parent0->status==0||$menu->parent0->status==1) ? $menu->parent0->route : null ;
                    $authorities[$menu->parent] = $_temp;
                }
            }
        }
        foreach ($authorities as $authority){
            $result['authorities'][] = $authority;
        }
        $roleInfo = [];
        $roles = AuthAssignment::find()->joinWith(['itemName'])->where(['user_id'=>\yii::$app->user->getId()])->all();
        foreach ($roles as $role){
            $temp = [];
            $temp['roleId'] = $role->item_name;
            $temp['roleCode'] = $role->item_name;
            $temp['roleName'] = $role->itemName->description;
            $temp['comments'] = $role->itemName->description;
            $temp['createTime'] = $role->itemName->created_at;
            $temp['updateTime'] = $role->itemName->updated_at;
            $roleInfo[] = $temp;
        }
        $result['roles'] = $roleInfo;
        return $result;
    }

    public static function getUsers(){
        $userinfo = [];
        $limit = \yii::$app->request->get('limit',10);
        $query = User::find()->joinWith(['roles','roles.itemName'])->where(['status'=>User::STATUS_ACTIVE]);
        $pages = new Pagination(['totalCount' => $query->count(),'pageSize'=>$limit]);
        $model = $query->offset($pages->offset)->limit($pages->limit)->all();
        foreach ($model as $value){
            $temp = [];
            $temp['userId'] = $value->id;
            $temp['email'] = $value->email;
            $temp['username'] = $value->username;
            $temp['nickname'] = $value->nickname;
            $temp['createTime'] = $value->created_at;
            $temp['updateTime'] = $value->updated_at;
            $temp['roles'] = [];
            foreach ($value->roles as $role){
                $_temp = [];
                $_temp['userId'] = $value->id;
                $_temp['roleId'] = $role->item_name;
                $_temp['roleCode'] = $role->item_name;
                $_temp['roleName'] = $role->itemName->description;
                $_temp['comments'] = $role->itemName->description;
                $_temp['createTime'] = $role->itemName->created_at;
                $temp['roles'][] = $_temp;
            }
            $userinfo[] = $temp;
        }
        return ['count'=>(int)$pages->totalCount,'list'=>$userinfo];
    }

    public static function getRoles(){
        $roleInfo = [];
        $limit = \yii::$app->request->get('limit',10);
        $query = AuthItem::find()->where(['type'=>1]);
        $pages = new Pagination(['totalCount' => $query->count(),'pageSize'=>$limit]);
        $model = $query->offset($pages->offset)->limit($pages->limit)->all();
        foreach ($model as $value){
            $temp = [];
            $temp['roleId'] = $value->name;
            $temp['roleCode'] = $value->name;
            $temp['roleName'] = $value->description;
            $temp['comments'] = $value->description;
            $temp['createTime'] = $value->created_at;
            $temp['updateTime'] = $value->updated_at;
            $roleInfo[] = $temp;
        }
        return ['count'=>(int)$pages->totalCount,'list'=>$roleInfo];
    }

    public static function getMenus(){
        $menuInfo = [];
        $query = Menu::find()->all();
        foreach ($query as $menu){
            $temp = [];
            $temp['icon'] = $menu->icon;
            $temp['menuId'] = $menu->id;
            $temp['authority'] = $menu->route;
            $temp['parentId'] = $menu->parent ?? 0;
            $temp['sortNumber'] = $menu->order ?? 1;
            $temp['hide'] = $menu->status == 1 ? 1 : 0;
            $temp['title'] = $menu->name ?? 'Undefined';
            $temp['menuType'] = $menu->status;
            $temp['path'] = $menu->route ;
            $temp['component'] = ($menu->status==0||$menu->status==1) ? $menu->route : null ;
            $menuInfo[] = $temp;
        }
        return $menuInfo;
    }
}