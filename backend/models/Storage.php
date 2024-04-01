<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dtb_storage".
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/Storage.php $
 * $Id: Storage.php 793 2015-03-14 00:32:51Z mori $
 *
 * @property integer $storage_id
 * @property integer $src_id
 * @property integer $dst_id
 * @property integer $staff_id
 * @property string $ship_date
 * @property string $pick_date
 * @property string $create_date
 * @property string $update_date
 *
 * @property MtbStaff $staff
 * @property MtbBranch $dst
 * @property MtbBranch $src
 * @property DtbStorageItem[] $dtbStorageItems
 */
class Storage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dtb_storage';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['src_id', 'dst_id', 'staff_id', 'ship_date', 'pick_date', 'create_date', 'update_date'], 'required'],
            [['src_id', 'dst_id', 'staff_id'], 'integer'],
            [['ship_date', 'pick_date', 'create_date', 'update_date'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'storage_id'  => 'ID',
            'src_id'      => '発送元',
            'dst_id'      => '宛先',
            'staff_id'    => '起票者',
            'ship_date'   => '発送日',
            'pick_date'   => '到着日',
            'create_date' => '起票日',
            'update_date' => '更新日',
        ];
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
    public function getDst()
    {
        return $this->hasOne(Branch::className(), ['branch_id' => 'dst_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrc()
    {
        return $this->hasOne(Branch::className(), ['branch_id' => 'src_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDtbStorageItems()
    {
        return $this->hasMany(StorageItem::className(), ['storage_id' => 'storage_id']);
    }
}
