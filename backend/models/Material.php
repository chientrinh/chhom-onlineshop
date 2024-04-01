<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mtb_material".
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/Material.php $
 * $Id: Material.php 804 2015-03-19 07:31:58Z mori $
 *
 * @property integer $material_id
 * @property string $name
 *
 * @property DtbMaterialInventoryItem[] $dtbMaterialInventoryItems
 * @property DtbInventory[] $inventories
 * @property MtbProductMaterial[] $mtbProductMaterials
 */
class Material extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mtb_material';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'material_id' => 'Material ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDtbMaterialInventoryItems()
    {
        return $this->hasMany(DtbMaterialInventoryItem::className(), ['material_id' => 'material_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventories()
    {
        return $this->hasMany(DtbInventory::className(), ['inventory_id' => 'inventory_id'])->viaTable('dtb_material_inventory_item', ['material_id' => 'material_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMtbProductMaterials()
    {
        return $this->hasMany(MtbProductMaterial::className(), ['material_id' => 'material_id']);
    }
}
