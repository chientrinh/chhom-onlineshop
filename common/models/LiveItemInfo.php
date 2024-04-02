<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "mtb_sales_category".
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/models/Streaming.php $
 * $Id: Streaming.php 2795 2020-04-20 11:55:11Z kawai $
 *
 * @property int  $streaming_id
 * @property int  $product_id
 * @property string  $name
 * @property string  $expire_from
 * @property string  $expire_to
 * @property string  $streaming_url
 * @property string  $post_url
 * @property string  $document_url
 * @property string  $create_date
 * @property string  $expire_date
 * @property string  $update_date
 *
 * @property Product $product
 */

class LiveItemInfo extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dtb_live_item_info';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'log' => [
                'class'  => ChangeLogger::className(),
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
            [['id','info_id','product_id'], 'integer'],
            [['product_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'info_id'  => "追加情報ID",
            'product_id'    => "ライブチケットの商品ID",
        ];
    }

    public function getInfoId()
    {
        return $this->info_id;
    }

    public function setInfoId($val)
    {
        $this->info_id = $val;
    }
    public function getProductId()
    {
        return $this->product_id;
    }

    public function setProductId($val)
    {
        $this->product_id = $val;
    }

    public function getName()
    {
        return $this->info->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(ProductMaster::className(), ['product_id' => 'product_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInfo()
    {
        return $this->hasOne(LiveInfo::className(), ['info_id' => 'info_id']);
    }

}

/*
class SalesCategoryQuery extends \yii\db\ActiveQuery
{
    public function forCampaign()
    {
        return $this->andWhere(['or', 
                                    ['company_id' => 3], 
                                    ['branch_id' => [Branch::PKEY_FRONT, Branch::PKEY_ATAMI, Branch::PKEY_ROPPONMATSU, Branch::PKEY_HJ_TOKYO, Branch::PKEY_EVENT]]
                    ]);
    }

    public function wareHouse()
    {
        return $this->andWhere(['branch_id' => [Branch::PKEY_ATAMI, Branch::PKEY_ROPPONMATSU]]);
    }
}
*/
