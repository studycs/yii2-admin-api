<?php
namespace app\models\entity;
use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string|null $nickname
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property-write mixed $password
 * @property-read string $authKey
 * @property-read AuthAssignment[] $roles
 * @property int $updated_at
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 10;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'auth_key', 'password_hash', 'email', 'created_at', 'updated_at'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['username'], 'string', 'max' => 32],
            [['nickname'], 'string', 'max' => 255],
            [['auth_key', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'nickname' => 'Nickname',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @param $id
     * @return User
     */
    public static function findIdentity($id)
    {
        return self::findOne(['id'=>$id,'status'=>self::STATUS_ACTIVE]);
    }

    /**
     * @param $token
     * @param $type
     * @return User
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user_token = UserToken::findOne(['token'=>$token]);
        if($user_token==null || $user_token->expired_at<=time()) return null;
        return self::findOne(['id'=>$user_token->user_id,'status'=>self::STATUS_ACTIVE]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param $authKey
     * @return bool
     */
    public function validateAuthKey($authKey)
    {
        return $authKey == $this->authKey;
    }

    public function setPassword($password){
        try{
            $this->password_hash = Yii::$app->security->generatePasswordHash($password);
        }catch(\Throwable $e){
            $this->password_hash = md5($password);
            \yii::warning('generatePasswordHash error ,'.$e->getMessage());
        }
    }

    public function validatePassword($password){
        try{
            return Yii::$app->security->validatePassword($password, $this->password_hash);
        }catch(\Throwable $e){
            return $this->password_hash == md5($password);
        }
    }

    public function generateAuthKey(){
        try{
            $this->auth_key = Yii::$app->security->generateRandomString();
        }catch(\Throwable $e){
            $this->auth_key = strtoupper(hash('sha256',uniqid()));
            \yii::warning('generateRandomString error ,'.$e->getMessage());
        }
    }

    public function generatePasswordResetToken(){
        try{
            $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
        }catch(\Throwable $e){
            \yii::warning('generateRandomString error ,'.$e->getMessage());
            $this->password_reset_token = strtoupper(hash('sha256',uniqid()));
        }
    }

    public function removePasswordResetToken(){
        $this->password_reset_token = null;
    }

    public static function findByUsername($username){
        return self::findOne(['username'=>$username,'status'=>self::STATUS_ACTIVE]);
    }

    public function getRoles(){
        return $this->hasMany(AuthAssignment::class,['user_id'=>'id']);
    }

    public static function isAdmin(){
        $userId = \yii::$app->user->getId();
        $_roles = \yii::$app->authManager->getRolesByUser($userId);
        return $userId==4 || isset($_roles['master']);
    }
}
