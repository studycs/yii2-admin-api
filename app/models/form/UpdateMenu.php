<?php

namespace app\models\form;

use app\models\entity\AuthItem;
use app\models\entity\AuthItemChild;
use app\models\entity\Menu;

class UpdateMenu extends \yii\base\Model
{
    public $authority;
    public $component;
    public $hide;
    public $icon;
    public $menuId;
    public $menuType;
    public $openType;
    public $parentId;
    public $path;
    public $sortNumber;
    public $title;

    public function rules()
    {
        return [
            [['authority','component','path','title','icon'],'string'],
            [['hide','menuType','openType','parentId','sortNumber','menuId'],'integer'],
            [['title','menuType','hide','sortNumber','menuId'],'required']
        ];
    }

    public function create(){
        if($this->validate()){
            $model = Menu::findOne(['id'=>$this->menuId]);
            if($this->path != $model->route) {
                AuthItem::deleteAll(['name'=>$model->route]);
                AuthItemChild::deleteAll(['child'=>$model->route]);
            }
            if($this->menuType==0){//显示菜单
                $model->icon = $this->icon;
                $model->route = $this->path;
                $model->name = $this->title;
                $model->parent = $this->parentId;
                $model->order = $this->sortNumber;
                $model->status = 0;
            }elseif($this->menuType==1){//隐藏类型
                $model->icon = $this->icon;
                $model->route = $this->path;
                $model->name = $this->title;
                $model->parent = $this->parentId;
                $model->order = $this->sortNumber;
                $model->status = 1;
            }elseif ($this->menuType==2){//按钮菜单
                $model->icon = $this->icon;
                $model->route = $this->path;
                $model->name = $this->title;
                $model->parent = $this->parentId;
                $model->order = $this->sortNumber;
                $model->status = 2;
            }else{
                $model->icon = $this->icon;
                $model->route = $this->path;
                $model->name = $this->title;
                $model->parent = $this->parentId;
                $model->order = $this->sortNumber;
                $model->status = 3;
            }
            if(!$model->save()) throw new \Exception(current($model->firstErrors)??'菜单新增失败！');
            $permission = AuthItem::findOne(['name'=>$model->route]);
            if($permission==null){
                $add = new AuthItem();
                $add->name = $model->route;
                $add->type = 2;
                $add->description = $model->name;
                $add->created_at = time();
                $add->updated_at = time();
                if(!$add->save()) throw new \Exception(current($add->firstErrors)??'菜单权限新增失败！');
            }
            return true;
        }
        return false;
    }

}