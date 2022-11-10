<?php

namespace app\models\entity;

use Yii;

/**
 * This is the model class for table "cmf_user".
 *
 * @property int $id
 * @property int $user_type 用户类型;1:admin;2:会员;3:前台用户
 * @property int $sex 性别;0:保密,1:男,2:女
 * @property int $birthday 生日
 * @property int $last_login_time 最后登录时间
 * @property int $score 用户积分
 * @property int $coin 金币
 * @property float $balance 余额
 * @property int|null $send_timeout 设置发送间隔
 * @property int $create_time 注册时间
 * @property int $user_status 用户状态;0:禁用,1:正常,2:未验证
 * @property string $user_login 用户名
 * @property string $user_pass 登录密码;cmf_password加密
 * @property string $user_nickname 用户昵称
 * @property string $user_email 用户登录邮箱
 * @property string $user_url 用户个人网址
 * @property string $avatar 用户头像
 * @property string $signature 个性签名
 * @property string $last_login_ip 最后登录ip
 * @property string $user_activation_key 激活码
 * @property string $mobile 中国手机不带国家代码，国际手机号格式为：国家代码-手机号
 * @property string|null $more 扩展属性
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cmf_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_type', 'sex', 'birthday', 'last_login_time', 'score', 'coin', 'send_timeout', 'create_time', 'user_status'], 'integer'],
            [['balance'], 'number'],
            [['more'], 'string'],
            [['user_login', 'user_activation_key'], 'string', 'max' => 60],
            [['user_pass'], 'string', 'max' => 64],
            [['user_nickname'], 'string', 'max' => 50],
            [['user_email', 'user_url'], 'string', 'max' => 100],
            [['avatar', 'signature'], 'string', 'max' => 255],
            [['last_login_ip'], 'string', 'max' => 15],
            [['mobile'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_type' => 'User Type',
            'sex' => 'Sex',
            'birthday' => 'Birthday',
            'last_login_time' => 'Last Login Time',
            'score' => 'Score',
            'coin' => 'Coin',
            'balance' => 'Balance',
            'send_timeout' => 'Send Timeout',
            'create_time' => 'Create Time',
            'user_status' => 'User Status',
            'user_login' => 'User Login',
            'user_pass' => 'User Pass',
            'user_nickname' => 'User Nickname',
            'user_email' => 'User Email',
            'user_url' => 'User Url',
            'avatar' => 'Avatar',
            'signature' => 'Signature',
            'last_login_ip' => 'Last Login Ip',
            'user_activation_key' => 'User Activation Key',
            'mobile' => 'Mobile',
            'more' => 'More',
        ];
    }
}
