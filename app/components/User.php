<?php
namespace app\components;
use app\models\entity\Users;

/**
 * @property-read array $ids
 * @property-read bool $isAdmin
 */
class User extends \yii\web\User
{
    public $identityClass = \app\models\entity\User::class;

    public $enableAutoLogin = true;

    public function getIsAdmin(){
        $userId = \yii::$app->user->getId();
        $_roles = \yii::$app->authManager->getRolesByUser($userId);
        return $userId==4 || isset($_roles['master']);
    }

    public function getIds(){
        $_uid = [\yii::$app->user->getId()];
        $user = Users::findOne(['user_login'=>\yii::$app->user->identity['username']]);
        if($user !=null) $_uid[] = $user->id;
        return $_uid;
    }

}