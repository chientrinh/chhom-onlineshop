<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mtb_material_maker".
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/MaterialMaker.php $
 * $Id: MaterialMaker.php 804 2015-03-19 07:31:58Z mori $
 *
 * @property integer $maker_id
 * @property string $name
 * @property string $manager
 * @property string $email
 * @property string $zip01
 * @property string $zip02
 * @property integer $pref_id
 * @property string $addr01
 * @property string $addr02
 * @property string $tel01
 * @property string $tel02
 * @property string $tel03
 *
 * @property DtbMaterialStorage[] $dtbMaterialStorages
 * @property MtbPref $pref
 */
class MaterialMaker extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mtb_material_maker';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'zip01', 'zip02', 'pref_id', 'addr01', 'addr02', 'tel01', 'tel02', 'tel03'], 'required'],
            [['pref_id'], 'integer'],
            [['name', 'manager'], 'string', 'max' => 45],
            [['email', 'addr01', 'addr02'], 'string', 'max' => 255],
            [['zip01'], 'string', 'max' => 3],
            [['zip02', 'tel02', 'tel03'], 'string', 'max' => 4],
            [['tel01'], 'string', 'max' => 5]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'maker_id' => 'Maker ID',
            'name' => 'Name',
            'manager' => 'Manager',
            'email' => 'Email',
            'zip01' => 'Zip01',
            'zip02' => 'Zip02',
            'pref_id' => 'Pref ID',
            'addr01' => 'Addr01',
            'addr02' => 'Addr02',
            'tel01' => 'Tel01',
            'tel02' => 'Tel02',
            'tel03' => 'Tel03',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDtbMaterialStorages()
    {
        return $this->hasMany(DtbMaterialStorage::className(), ['maker_id' => 'maker_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPref()
    {
        return $this->hasOne(MtbPref::className(), ['pref_id' => 'pref_id']);
    }
}
