<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "dtb_live_info".
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/models/LiveInfo.php $
 * $Id: LiveInfo.php 2795 2020-04-20 11:55:11Z kawai $
 *
 * @property int  $info_id
 * @property int  $coupon_discount
 * @property int  $option_product_id //$option_price
 * @property string  $coupon_name
 * @property string  $coupon_code
 * @property int   $online_coupon_enable
 * @property string  $place
 * @property string  $option_name
 * @property string  $option_description
 * @property int     $online_option_enable
 * @property string  $create_date
 * @property string  $expire_date
 * @property string  $update_date
 *
 */

class LiveInfo extends \yii\db\ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dtb_live_info';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'update'=>[
                'class' => \yii\behaviors\AttributeBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['create_date','update_date'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => 'update_date',
                ],
                'value' => function ($event) {
                    return new \yii\db\Expression('NOW()');
                },
            ],
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
            [['name'], 'required'],
            [['coupon_discount','online_coupon_enable','online_option_enable','adult_price1','adult_price2','adult_price3','child_price1','child_price2','child_price3','infant_price1','infant_price2','infant_price3','capacity','subscription','campaign_type','support_entry'], 'integer'],
            // [['option_price'], 'integer'],
            ['product_id', 'product_check'],
            [['name','place','option_name','option_description','coupon_name','coupon_code','campaign_code','campaign_period','pre_order_code','pre_order_period'], 'string', 'max' => 255],
            ['campaign_type',  'validateCampaign'],
            [['create_date','expire_date','product_id','product'], 'safe'],
            ['place','place_check'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'info_id'  => "追加情報ID",
            'name'          => "管理用名称",
            'place'   => "会場（カンマ区切り）",
            'option_name'     => "オプション名",
            'option_description' => "オプション注意事項",
            'online_option_enable'      => "自宅受講時オプション",
            'product_id'      => "オプション商品",
            'coupon_name'      => "クーポン名",
            'coupon_code'   => "クーポンコード",
            'coupon_discount'   => "クーポン値引き額",
            'online_coupon_enable'      => "自宅受講時クーポン",
            'companion'    => "同行者",
            'adult_price1'    => "参加費・大人・税込み",
            'adult_price2'    => "参加費・大人・税別",
            'adult_price3'    => "参加費・大人・消費税",
            'child_price1'    => "参加費・小人・税込み",
            'child_price2'    => "参加費・小人・税別",
            'child_price3'    => "参加費・小人・消費税",
            'infant_price1'    => "参加費・未就学児・税込み",
            'infant_price2'    => "参加費・未就学児・税別",
            'infant_price3'    => "参加費・未就学児・消費税",
            'capacity'    => "定員",
            'subscription'    => "申込人数",
            'campaign_code'    => "キャンペーンコード",
            'campaign_type'    => "キャンペーン種類",
            'campaign_period'    => "キャンペーン期間",
            'pre_order_code'    => "事前注文",
            'pre_order_period'    => "事前注文受付期間",
            'support_entry'    => "サポート申込区分",
            'expire_date'   => "公開終了日",
            'create_date'   => "作成日時",
            'update_date'   => "更新日時",
        ];
    }

    public function place_check($attribute,$params)
    {
      if(strpos($this->place, '，') !== false){
        $this->place = str_replace('，',',', $this->place);
        // $this->addError('place','全角カンマは半角に変換します');
        // return false;
      }

      if(substr($this->place, -1) == ','){
        $this->place = substr($this->place, 0, strlen($this->place)-1);
      }

      return true;

    }

    public function product_check($attribute,$params)
    {
      if(!Product::find(['product_id'=>$attribute])){
        $this->addError('product_id','存在する商品を指定してください');
        return false;
      }

      return true;

    }

    public function validateCampaign($attr, $params)
    {
        if($this->campaign_type == null || $this->campaign_type == 0) {
            if (($this->campaign_code != null && $this->campaign_code != "") || ($this->campaign_period != null && $this->campaign_period != ""))
                $this->addError($attr, "キャンペーン情報を設定する場合、キャンペーン種類も必ず選択してください");
        }
        return $this->hasErrors($attr);
    }


    public function validate($attributeNames = null, $clearErrors = true)
    {

        if(!parent::validate($attributeNames, $clearErrors)) {
           return false;
        }
        return $this->hasErrors() ? false : true;
    }


    public function beforeSave($insert)
    {
        // Campaign_idが０なら、NULLとして登録する
        if(0 === $this->product_id)
            $this->product_id = null;

        return parent::beforeSave($insert);
    }  


    public function isExpired()
    {
        return (strtotime($this->expire_date) <= time());
    }




    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLiveItemInfos()
    {
        return $this->hasMany(LiveItemInfo::className(), ['info_id' => 'info_id']);
    }
    
    /**
     * 
     */
    public function getProduct()
    {
        return Product::findOne($this->product_id);
    }

    public function setProduct($product)
    {
        $this->product = $product;
    }
}

