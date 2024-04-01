<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dtb_manufacture".
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/Manufacture.php $
 * $Id: Manufacture.php 804 2015-03-19 07:31:58Z mori $
 *
 * @property integer $manufact_id
 * @property integer $branch_id
 * @property integer $staff_id
 * @property integer $quantity
 * @property string $create_date
 *
 * @property MtbBranch $branch
 * @property MtbStaff $staff
 * @property DtbManufactureItem[] $dtbManufactureItems
 */
class Manufacture extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dtb_manufacture';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['branch_id', 'staff_id', 'quantity', 'create_date'], 'required'],
            [['branch_id', 'staff_id', 'quantity'], 'integer'],
            [['create_date'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'manufact_id' => 'Manufact ID',
            'branch_id' => 'Branch ID',
            'staff_id' => 'Staff ID',
            'quantity' => 'Quantity',
            'create_date' => 'Create Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(MtbBranch::className(), ['branch_id' => 'branch_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStaff()
    {
        return $this->hasOne(MtbStaff::className(), ['staff_id' => 'staff_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDtbManufactureItems()
    {
        return $this->hasMany(DtbManufactureItem::className(), ['manufact_id' => 'manufact_id']);
    }
}
