<?php
namespace backend\models\stat;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/stat/MonthlySummary.php $
 * $Id: MonthlySummary.php 2018-08-29  mori $
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

use Yii;
use \common\models\Branch;
use \common\models\Company;
use \common\models\Category;
use \common\models\Payment;

class MonthlySummary extends \yii\db\ActiveRecord
{
/*
    public $year;
    public $month;
    public $company_id; // 所属会社
    public $branch_id;
    public $category_id; // カテゴリー
    public $seller_id; // 販売会社
    public $payment_id;
*/

    public function init()
    {
        parent::init();

        if(! isset($this->year))
            $this->year = date('Y');

        if(! isset($this->month))
            $this->month = date('m');
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dtb_monthly_summary';
    }


    public function rules()
    {
        return [
            ['payment_id', 'exist', 'targetClass'=>Payment::className(), 'targetAttribute'=>'payment_id'],
            ['company_id', 'exist', 'targetClass'=>Company::className(), 'targetAttribute'=>'company_id'],
            ['branch_id',  'exist', 'targetClass'=> Branch::className(), 'targetAttribute'=> 'branch_id'],
            ['seller_id',  'exist', 'targetClass'=> Company::className(), 'targetAttribute'=> 'company_id'],
            [['year', 'month'], 'integer'],
            [['seller_id', 'category_id'], 'default', 'value' => null],
            [['subtotal', 'return_total', 'discount_total', 'total_charge', 'tax_total', 'discount', 'point_consume', 'point_given', 'postage', 'handling', 'net_sales', 'quantity'], 'default', 'value'=>0],
        ];
    }

    public function attributeLabels()
    {
        return [
            'year'             => "年",
            'month'            => "月",
            'company_id'       => "所属会社",
            'branch_id'        => "拠点",
            'category_id'      => "カテゴリー",
            'seller_id'        => "販売会社",
            'subtotal'         => "商品計",
            'return_total'     => "返品計",
            'discount_total'   => "値引計",
            'total_charge'     => "売上合計",
            'tax_total'        => "消費税",
            'discount'         => '値引き',
            'point_consume'    => 'ポイント値引き',
            'point_given'      => '付与ポイント',
            'postage'          => '送料',
            'handling'         => '手数料',
            'net_sales'        => "純売上（税込）",
            'quantity'         => "数量",
            'create_date'      => "作成日",
            'update_date'      => "更新日",
        ];
    }


    public function getDiscount()
    {
        return $this->discount;
    }

    public function getPointConsume()
    {
        return $this->point_consume;
    }

    public function getPointGiven()
    {
        return $this->point_given;
    }

    public function getBasePrice()
    {
        return $this->subtotal;
    }

    public function getDiscountTotal()
    {
        return $this->discount_total;
    }

    public function getTaxTotal()
    {
        return $this->tax_total;
    }

    public function getReturnCharge()
    {
        return $this->return_total;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['branch_id' => 'branch_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['company_id' => 'company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['category_id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['payment_id' => 'payment_id']);
    }
}
