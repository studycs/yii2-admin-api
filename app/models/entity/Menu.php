<?php

namespace app\models\entity;

use Yii;

/**
 * This is the model class for table "menu".
 *
 * @property int $id
 * @property string|null $name 菜单名称
 * @property int|null $parent 父级菜单ID
 * @property string|null $route 菜单路由
 * @property int|null $order 菜单排序
 * @property string|null $data 菜单扩展数据列
 * @property string|null $icon 菜单icon
 * @property int|null $status 0:不隐藏可以访问，1:隐藏可以访问，2:菜单被禁用
 *
 * @property Menu[] $menus
 * @property Menu $parent0
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'menu';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent', 'order', 'status'], 'integer'],
            [['data'], 'safe'],
            [['name'], 'string', 'max' => 128],
            [['route'], 'string', 'max' => 256],
            [['icon'], 'string', 'max' => 20],
            [['parent'], 'exist', 'skipOnError' => true, 'targetClass' => Menu::class, 'targetAttribute' => ['parent' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'parent' => 'Parent',
            'route' => 'Route',
            'order' => 'Order',
            'data' => 'Data',
            'icon' => 'Icon',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[Menus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMenus()
    {
        return $this->hasMany(Menu::class, ['parent' => 'id']);
    }

    /**
     * Gets query for [[Parent0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent0()
    {
        return $this->hasOne(Menu::class, ['id' => 'parent']);
    }
}
