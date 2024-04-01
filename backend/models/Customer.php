<?php
namespace backend\models;

use Yii;

/**
 * This is the wrapper class for table "dtb_customer".
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/Customer.php $
 * $Id: Customer.php 4154 2019-04-12 06:14:10Z mori $
 *
 */
class Customer extends \common\models\Customer
{
    public $is_agency;
    public $agencies;
    public $is_active;
 
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInfos()
    {
	return $this->hasMany(CustomerInfo::className(), ['customer_id' => 'customer_id'])->andWhere(['not in','created_by',0]);
    }

    public function getMigratives()
    {
        return $this->hasMany(\common\models\Membercode::className(), ['customer_id' => 'customer_id'])->andWhere(['not',['status'=>0]])->inverseOf('customer')->orderBy('status DESC');
    }

    public function getNext()
    {
        return static::find()
            ->andWhere(['>','customer_id', $this->customer_id])
            ->orderBy('customer_id ASC')
            ->one();
    }

    public function getPrev()
    {
        return static::find()
            ->andWhere(['<','customer_id', $this->customer_id])
            ->orderBy('customer_id DESC')
            ->one();
    }
    
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['email','allowEmptyValidation','skipOnEmpty'=>false,'skipOnError'=>false];

        return $rules;
    }

    public function allowEmptyValidation($attr, $params)
    {
        if(0 == strlen($this->$attr))
            $this->clearErrors($attr);

        return true;
    }

    public function attributeHints()
    {
        $hint = parent::attributeHints();
        $hint['grade'] = '自動設定のため、直接変更できません';

        if($this->getParent()->exists())
            $hint['code'] = '家族会員の場合、自動設定のため変更できません';

        return $hint;
    }

    public static function findByBarcode($ean13, $strict=false, $auto_customer_create = '0')
    {
        if($model = parent::findByBarcode($ean13, $strict))
            return $model;


        if($attr = self::parseBarcode($ean13))
            $model = \common\models\Membercode::findOne([
                'code'       => $attr['code'],
                'migrate_id' => null,
                'directive'  => null,
            ]);

        if($model && '1' != $auto_customer_create) 
            return $model;

         // 会員証テーブルのレコードに顧客が紐付いていない場合、新規作成して紐付ける処理・ticket:726 により制御追加
          if('1' != $auto_customer_create) {
              return null;
          } else {
            $customer = new self([
                'grade_id' => \common\models\CustomerGrade::PKEY_AA,
                'name01' => '',
                'name02' => '',
                'kana01' => '',
                'kana02' => '',
                'email'  => '',
                'zip01'  => '',
                'zip02'  => '',
                'addr01' => '',
                'addr02' => '',
                'tel01'  => '',
                'tel02'  => '',
                'tel03'  => '',
                'password_hash' => '',
            ]);
            $customer->detachBehavior('membercode');
            $customer->save(false);

            $model->customer_id = $customer->customer_id;
            $model->save();

            return $customer;
        }
    }

    public function getdtb_customer_membership()
    {
        return $this->hasMany(\common\models\CustomerMembership::className(), ['customer_id' => 'customer_id']);        
    }
}
