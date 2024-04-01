<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "mtb_role".
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/Role.php $
 * $Id: Role.php 2731 2016-07-17 01:41:14Z mori $
 *
 * @property integer $role_id
 * @property string $name
 *
 * @property MtbStaffRole[] $mtbStaffRoles
 */
class Role extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mtb_role';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'log' => [
                'class'  => \common\models\ChangeLogger::className(),
                'owner'  => $this,
                'user'   => Yii::$app->has('user') ? Yii::$app->user : null,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'role_id' => 'Role ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStaffs()
    {
        return $this->hasMany(StaffRole::className(), ['role_id' => 'role_id']);
    }
}
