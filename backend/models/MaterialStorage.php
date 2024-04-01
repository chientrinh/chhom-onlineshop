<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dtb_material_storage".
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/MaterialStorage.php $
 * $Id: MaterialStorage.php 804 2015-03-19 07:31:58Z mori $
 *
 * @property integer $material_id
 * @property integer $cost
 * @property integer $quantity
 * @property integer $unit
 * @property integer $maker_id
 * @property integer $staff_id
 * @property string $create_date
 *
 * @property MtbMaterialMaker $maker
 * @property MtbStaff $staff
 */
class MaterialStorage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dtb_material_storage';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['material_id', 'quantity', 'unit', 'maker_id', 'staff_id', 'create_date'], 'required'],
            [['material_id', 'cost', 'quantity', 'unit', 'maker_id', 'staff_id'], 'integer'],
            [['create_date'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'material_id' => 'Material ID',
            'cost' => 'Cost',
            'quantity' => 'Quantity',
            'unit' => 'Unit',
            'maker_id' => 'Maker ID',
            'staff_id' => 'Staff ID',
            'create_date' => 'Create Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaker()
    {
        return $this->hasOne(MtbMaterialMaker::className(), ['maker_id' => 'maker_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStaff()
    {
        return $this->hasOne(MtbStaff::className(), ['staff_id' => 'staff_id']);
    }
}
