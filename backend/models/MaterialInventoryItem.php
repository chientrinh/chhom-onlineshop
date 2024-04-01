<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dtb_material_inventory_item".
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/MaterialInventoryItem.php $
 * $Id: MaterialInventoryItem.php 804 2015-03-19 07:31:58Z mori $
 *
 * @property integer $inventory_id
 * @property integer $material_id
 * @property integer $actual_qty
 * @property integer $waste_qty
 *
 * @property DtbInventory $inventory
 * @property MtbMaterial $material
 */
class MaterialInventoryItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dtb_material_inventory_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['inventory_id', 'material_id', 'actual_qty', 'waste_qty'], 'required'],
            [['inventory_id', 'material_id', 'actual_qty', 'waste_qty'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'inventory_id' => 'Inventory ID',
            'material_id' => 'Material ID',
            'actual_qty' => 'Actual Qty',
            'waste_qty' => 'Waste Qty',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventory()
    {
        return $this->hasOne(DtbInventory::className(), ['inventory_id' => 'inventory_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial()
    {
        return $this->hasOne(MtbMaterial::className(), ['material_id' => 'material_id']);
    }
}
