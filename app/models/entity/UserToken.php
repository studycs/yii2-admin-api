<?php
namespace app\models\entity;

use Yii;

/**
 * This is the model class for table "user_token".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $token
 * @property int|null $expired_at
 * @property int|null $created_at
 */
class UserToken extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_token';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'expired_at', 'created_at'], 'integer'],
            [['token'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'token' => 'Token',
            'expired_at' => 'Expired At',
            'created_at' => 'Created At',
        ];
    }
}
