<?php
namespace app\models\form;
use Yii;
use yii\base\Model;
use app\models\entity\User;
use app\models\entity\UserToken;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username'=>\yii::t('app','username'),
            'password'=>\yii::t('app','password'),
            'rememberMe'=>\yii::t('app','rememberMe'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function login()
    {
        if ($this->validate()) {
            $model = new UserToken();
            $model->created_at = time();
            $model->user_id = $this->user->getId();
            $model->expired_at = time() + 3600*24*30;
            $model->token = strtoupper(hash_hmac('sha256',uniqid(),time()));
            if(!$model->save())  throw new \Exception(current($model->firstErrors) ?? '登录失败');
            return ['access_token'=>$model->token];
        }
        throw new \Exception(current($this->firstErrors)??'用户名或密码错误');
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
