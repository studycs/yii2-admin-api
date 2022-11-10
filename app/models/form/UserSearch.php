<?php

namespace app\models\form;

use app\models\entity\User;

class UserSearch extends \yii\base\Model
{
    public $role;
    public $limit;
    public $username;

    public function rules()
    {
        return [
            [['limit'],'integer','max'=>1000],
            [['role','username'],'string','max'=>20]
        ];
    }

    public function search(){
        $model = User::find();
        if(!empty($this->username)) {
            $model->andFilterWhere(['username'=>$this->username]);
        }
    }
}