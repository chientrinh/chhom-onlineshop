<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dtb_material_inventory".
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/MaterialInventory.php $
 * $Id: MaterialInventory.php 804 2015-03-19 07:31:58Z mori $
 *
 * @property integer $inventory_id
 * @property integer $branch_id
 * @property integer $staff_id
 * @property string $create_date
 * @property string $update_date
 *
 * @property MtbBranch $branch
 * @property MtbStaff $staff
 */
class MaterialInventory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dtb_material_inventory';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['branch_id', 'staff_id', 'create_date', 'update_date'], 'required'],
            [['branch_id', 'staff_id'], 'integer'],
            [['create_date', 'update_date'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'inventory_id' => 'Inventory ID',
            'branch_id' => 'Branch ID',
            'staff_id' => 'Staff ID',
            'create_date' => 'Create Date',
            'update_date' => 'Finish Date',
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
}
