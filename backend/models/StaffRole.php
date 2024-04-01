<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "mtb_staff_role".
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/StaffRole.php $
 * $Id: StaffRole.php 2731 2016-07-17 01:41:14Z mori $
 *
 * @property integer $branch_id
 * @property integer $staff_id
 * @property integer $role_id
 *
 * @property MtbBranch $branch
 * @property MtbStaff $staff
 * @property MtbRole $role
 */
class StaffRole extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mtb_staff_role';
    }

    public static function primaryKey()
    {
        return ['staff_id','role_id','branch_id'];
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
            [['staff_id', 'role_id'], 'required'],
            [['branch_id', 'staff_id', 'role_id'], 'integer'],
            ['staff_id','exist','targetClass'=>Staff::className()],
            ['role_id','exist','targetClass'=>Role::className()],
            ['branch_id','exist','targetClass'=>\common\models\Branch::className()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'branch_id' => '拠点',
            'staff_id'  => '従業員',
            'role_id'   => '役割',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(\common\models\Branch::className(), ['branch_id' => 'branch_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStaff()
    {
        return $this->hasOne(Staff::className(), ['staff_id' => 'staff_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::className(), ['role_id' => 'role_id']);
    }
}
