<?php
namespace backend\models\stat;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/stat/DailySummary.php $
 * $Id: DailySummary.php 2018-08-29  mori $
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

use Yii;
use \common\models\Branch;
use \common\models\Company;
use \common\models\Purchase;
use \common\models\PurchaseItem;
use \common\models\PurchaseStatus;
use \common\models\RemedyVial;
use \common\models\Category;

class DailySummary extends \yii\base\Model
{
    public $start_date;
    public $end_date;
    public $company_id;
    public $branch_id;
    public $class; // 分類
    public $payment_id;

    private $_purchaseCount;
    private $_itemCount;

    public function init()
    {
        parent::init();

        if(! isset($this->start_date))
            $this->start_date = date('Y-m-d 00:00:00');

        if(! isset($this->end_date))
            $this->end_date = date('Y-m-d 23:59:59');
    }

    public function rules()
    {
        return [
            ['company_id', 'exist', 'targetClass'=>Company::className(), 'targetAttribute'=>'company_id'],
            ['branch_id',  'exist', 'targetClass'=> Branch::className(), 'targetAttribute'=> 'branch_id'],
            [['start_date','end_date'], 'date'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'start_date'  => "開始",
            'end_date'    => "終了",
            'itemCount'   => "品数",
            'purchaseCount' => "伝票",
            'share'         => "割合",
            'totalCharge'   => "お支払い合計",
            'subtotal'      => "商品計",
            'tax'           => "消費税",
            'pointConsume'  => "ポイント値引き",
            'pointGiven'    => "付与ポイント",
            'postage'       => "送料",
            'handling'      => "手数料",
            'discount'      => "値引き",
            'receive'       => "お預かり",
            'change'        => "おつり",
            'payment_id'    => "お支払い",
            'shipped'       => "発送の状態",
            'create_date'   => "購入日",
            'update_date'   => "更新日",
            'shipping_date' => "発送日",
            'note'          => "当社コメント",
            'customer_msg'  => "お客様の言葉",
            'status'        => "状況",
            'is_wholesale'  => "卸売",
        ];
    }

    public function getQuery()
    {
        $query = Purchase::find()->active()
                                 ->andWhere(['between', 'create_date', $this->start_date, $this->end_date]);

        if(!is_null($this->branch_id) && $this->branch_id !== '99')
            self::narrowQuery($query, $this->branch_id);

        return $query;
    }
    
    public function getHeaderItemQuery()
    {
        $query = $this->query
                ->select([
                        '(CASE WHEN company_id IS NULL THEN (SELECT company_id FROM mtb_branch WHERE branch_id = dtb_purchase.branch_id) ELSE company_id END) AS summary_company_id',
                        'branch_id',
                        'SUM(discount) AS discount',
                        'SUM(point_consume) AS point_consume',
                        'SUM(point_given) AS point_given',
                        'SUM(postage) AS postage',
                        'SUM(handling) AS handling'
                    ])
                ->groupBy(['summary_company_id', 'branch_id'])
                ->orderBy(['summary_company_id' => SORT_ASC, 'branch_id' => SORT_ASC]);

        if($this->payment_id)
            $query->andWhere(['payment_id' => $this->payment_id]);

        if($this->company_id){
            $query->andWhere(['(CASE WHEN company_id IS NULL THEN (SELECT company_id FROM mtb_branch WHERE branch_id = dtb_purchase.branch_id) ELSE company_id END)' => $this->company_id]);
        }

        if(!is_null($this->branch_id) && $this->branch_id !== '99'){
            $query->andWhere(['branch_id' => $this->branch_id]);
        }
        return $query;
    }

    public function getPointQuery()
    {
        $query = \common\models\Pointing::find()->active()
                ->andWhere(['between', 'create_date', $this->start_date, $this->end_date])
                ->andWhere(['<>', 'note', ''])
                ->select([
                    'company_id',
                    '(SELECT 99) AS branch_id',
                    'SUM(point_consume) AS point_consume',
                    'SUM(point_given) AS point_given',
                ])
                ->groupBy(['company_id'])
                ->orderBy(['company_id' => SORT_ASC]);

        if($this->company_id){
            $query->andWhere(['company_id' => $this->company_id]);
        }

        if (!is_null($this->branch_id) && $this->branch_id !== '99') {
            // 拠点指定の場合は表示させないようにするため適当なwhere句を追加
            $query->andWhere(['pointing_id' => '0']);
        }
        return $query;
    }
    
    public function getItemQuery()
    {
        // 野菜以外の集計を取得
        $query = PurchaseItem::find()
               ->from(PurchaseItem::tableName() . ' t')
               ->innerJoinWith('purchase')
               ->andWhere(['between','dtb_purchase.create_date',$this->start_date, $this->end_date])
               ->andWhere(['between','dtb_purchase.status',PurchaseStatus::PKEY_INIT, PurchaseStatus::PKEY_DONE])
               ->andWhere(['<>', 't.minus_product', 1])
               ->andWhere('t.code not like :query', [':query' => \common\models\Vegetable::EAN13_PREFIX . '0%'])
               ->andWhere('t.product_id IS NOT NULL')
               ->select([
                   'SUM(t.quantity) as quantity',
                   'SUM(t.quantity * (t.unit_price + t.discount_amount)) as basePrice',
                   'SUM(t.quantity * floor(t.price * point_rate    /100)) as pointTotal',
                   'SUM(t.quantity * t.discount_amount) as discountTotal',
                   'SUM(t.quantity * t.unit_tax) as taxTotal',
                   'dtb_purchase.branch_id',
                   '(SELECT CASE WHEN p.liquor_flg = 1 THEN 98 WHEN code LIKE "231%" THEN 5 ELSE c.category_id END FROM mtb_category c INNER JOIN dtb_product p ON p.category_id = c.category_id WHERE p.product_id = t.product_id) AS category_id',
                   '(SELECT CASE '
                       . 'WHEN dtb_purchase.branch_id = 6 THEN t.company_id '
                       . 'WHEN category_id = 3 THEN 4 '
                       . 'ELSE company_id END '
                       . 'FROM mtb_branch WHERE branch_id = dtb_purchase.branch_id) as company_id',
                   'SUM(t.point_amount * t.quantity) as point_given',
                   '(SELECT 0) as discount',
                   '(SELECT 0) as point_consume',
                   '(SELECT 0) as postage',
                   '(SELECT 0) as handling',
                   '(SELECT 0) AS returnCharge'
               ])
              ->groupBy(['company_id', 'dtb_purchase.branch_id', 'category_id'])
              ->orderBy(['company_id' => SORT_ASC, 'dtb_purchase.branch_id' => SORT_ASC, 'category_id' => SORT_ASC]);

        // 野菜の集計を取得。先頭が２３で、７桁目まで（２３＋野菜ID）の集計
        $query2 = PurchaseItem::find()
               ->from(PurchaseItem::tableName() . ' t')
               ->innerJoinWith('purchase')
               ->andWhere(['between','dtb_purchase.create_date',$this->start_date, $this->end_date])
               ->andWhere(['between','dtb_purchase.status',PurchaseStatus::PKEY_INIT, PurchaseStatus::PKEY_DONE])
               ->andWhere(['<>', 't.minus_product', 1])
               ->andWhere('t.code like :query', [':query' => \common\models\Vegetable::EAN13_PREFIX . '0%'])
               ->select([
                   'SUM(t.quantity) as quantity',
                   'SUM(t.quantity * (t.unit_price + t.discount_amount)) as basePrice',
                   'SUM(t.quantity * floor(t.price * point_rate    /100)) as pointTotal',
                   'SUM(t.quantity * t.discount_amount) as discountTotal',
                   'SUM(t.quantity * t.unit_tax) as taxTotal',
                   'dtb_purchase.branch_id',
                   '(SELECT 99) AS category_id',
                   '(SELECT company_id FROM mtb_branch WHERE branch_id = dtb_purchase.branch_id) as company_id',
                   'SUM(t.point_amount * t.quantity) as point_given',
                   '(SELECT 0) as discount',
                   '(SELECT 0) as point_consume',
                   '(SELECT 0) as postage',
                   '(SELECT 0) as handling',
                   '(SELECT 0) AS returnCharge'
               ])
              ->groupBy(['company_id', 'dtb_purchase.branch_id', 'category_id'])
              ->orderBy(['company_id' => SORT_ASC, 'dtb_purchase.branch_id' => SORT_ASC, 'category_id' => SORT_ASC]);

        // レメディーテーブル管理の商品を取得
        $query3 = PurchaseItem::find()
               ->from(PurchaseItem::tableName() . ' t')
               ->innerJoinWith('purchase')
               ->andWhere(['between','dtb_purchase.create_date',$this->start_date, $this->end_date])
               ->andWhere(['between','dtb_purchase.status',PurchaseStatus::PKEY_INIT, PurchaseStatus::PKEY_DONE])
               ->andWhere(['<>', 't.minus_product', 1])
               ->andWhere('t.code not like :query', [':query' => \common\models\Vegetable::EAN13_PREFIX . '0%'])
               ->andWhere('t.product_id IS NULL')
               ->select([
                   'SUM(t.quantity) as quantity',
                   'SUM(t.quantity * (t.unit_price + t.discount_amount)) as basePrice',
                   'SUM(t.quantity * floor(t.price * point_rate    /100)) as pointTotal',
                   'SUM(t.quantity * t.discount_amount) as discountTotal',
                   'SUM(t.quantity * t.unit_tax) as taxTotal',
                   'dtb_purchase.branch_id',
                   '(SELECT CASE WHEN vial_id IN (5, 6, 7, 8, 9, 11, 12) THEN 98 ELSE 6 END FROM mvtb_product_master WHERE ean13 = t.code'
                       . ' UNION ALL SELECT DISTINCT 6 FROM mvtb_product_master'
                       . ' WHERE NOT EXISTS (SELECT * FROM mvtb_product_master WHERE ean13 = t.code)) AS category_id',
                   '(SELECT CASE '
                       . 'WHEN dtb_purchase.branch_id = 6 THEN t.company_id '
                       . 'ELSE company_id END '
                       . 'FROM mtb_branch WHERE branch_id = dtb_purchase.branch_id) as company_id',
                   'SUM(t.point_amount * t.quantity) as point_given',
                   '(SELECT 0) as discount',
                   '(SELECT 0) as point_consume',
                   '(SELECT 0) as postage',
                   '(SELECT 0) as handling',
                   '(SELECT 0) AS returnCharge'
               ])
              ->groupBy(['company_id', 'dtb_purchase.branch_id', 'category_id'])
              ->orderBy(['company_id' => SORT_ASC, 'dtb_purchase.branch_id' => SORT_ASC, 'category_id' => SORT_ASC]);

        if(!is_null($this->branch_id) && $this->branch_id !== '99'){
            self::narrowQuery($query, $this->branch_id);
            self::narrowQuery($query2, $this->branch_id);
            self::narrowQuery($query3, $this->branch_id);
        }

        if($this->company_id){
            $query->andWhere(['(SELECT CASE '
                                . 'WHEN dtb_purchase.branch_id = 6 THEN t.company_id '
                                . 'WHEN (SELECT CASE WHEN p.liquor_flg = 1 THEN 98 WHEN code LIKE "231%" THEN 5 ELSE c.category_id END FROM mtb_category c INNER JOIN dtb_product p ON p.category_id = c.category_id WHERE p.product_id = t.product_id) = 3 THEN 4 '
                                . 'ELSE company_id END '
                                . 'FROM mtb_branch WHERE branch_id = dtb_purchase.branch_id)' => $this->company_id]);
            $query2->andWhere(['(SELECT company_id FROM mtb_branch WHERE branch_id = dtb_purchase.branch_id)' => $this->company_id]);
            $query3->andWhere(['(SELECT CASE '
                                . 'WHEN dtb_purchase.branch_id = 6 THEN t.company_id '
                                . 'ELSE company_id END '
                                . 'FROM mtb_branch WHERE branch_id = dtb_purchase.branch_id)' => $this->company_id]);
        }

        if ($this->payment_id) {
            $query->andWhere(['dtb_purchase.payment_id' => $this->payment_id]);
            $query2->andWhere(['dtb_purchase.payment_id' => $this->payment_id]);
            $query3->andWhere(['dtb_purchase.payment_id' => $this->payment_id]);
        }
        
        if($this->class) {
            if($this->class == 0) {
            }
                
            if($this->class == 1) {
                $query->innerJoinWith('stocks'); // 酒類判定のため

                $query->andWhere(['not',['t.remedy_id' => 0]]);
                $query->andWhere(['in', 'vial_id', [RemedyVial::GLASS_5ML,
                    RemedyVial::GLASS_SPRAY_10ML,
                    RemedyVial::GLASS_20ML,
                    RemedyVial::GLASS_150ML,
                    RemedyVial::GLASS_720ML,
                    RemedyVial::PLASTIC_SPRAY_100ML,
                    RemedyVial::PLASTIC_SPRAY_50ML]]);

            }
            if($this->class == 2) {
                $query->innerJoinWith('product'); // レストラン判定のため
                $query->andWhere(['category_id' => Category::RESTAURANT]);
            }

            $query->groupBy('t.code');
            return $query;
        }
        $query->union($query2);
        $query->union($query3);
        return $query;
    }

    /**
     * 返品伝票を集計
     * @return type
     */
    public function getReturnItemQuery()
    {
        // 野菜以外の集計を取得
        $query = PurchaseItem::find()
               ->from(PurchaseItem::tableName() . ' t')
               ->innerJoinWith('purchase')
               ->andWhere(['between','dtb_purchase.create_date',$this->start_date, $this->end_date])
               ->andWhere(['dtb_purchase.status' => PurchaseStatus::PKEY_RETURN])
               ->andWhere(['<>', 't.minus_product', 1])
               ->andWhere('t.code not like :query', [':query' => \common\models\Vegetable::EAN13_PREFIX . '0%'])
               ->andWhere('t.product_id IS NOT NULL')
               ->select([
                   'SUM(ABS(t.quantity)) as quantity',
                   'ABS(SUM(t.quantity * t.unit_price)) as basePrice',
                   'dtb_purchase.branch_id',
                   '(SELECT CASE WHEN p.liquor_flg = 1 THEN 98 WHEN code LIKE "231%" THEN 5 ELSE c.category_id END FROM mtb_category c INNER JOIN dtb_product p ON p.category_id = c.category_id WHERE p.product_id = t.product_id) AS category_id',
                   '(SELECT CASE '
                       . 'WHEN dtb_purchase.branch_id = 6 THEN t.company_id '
                       . 'WHEN category_id = 3 THEN 4 '
                       . 'ELSE company_id END '
                       . 'FROM mtb_branch WHERE branch_id = dtb_purchase.branch_id) as company_id',
               ])
              ->groupBy(['company_id', 'dtb_purchase.branch_id', 'category_id'])
              ->orderBy(['company_id' => SORT_ASC, 'dtb_purchase.branch_id' => SORT_ASC, 'category_id' => SORT_ASC]);

        // 野菜の集計を取得。先頭が２３で、７桁目まで（２３＋野菜ID）の集計
        $query2 = PurchaseItem::find()
               ->from(PurchaseItem::tableName() . ' t')
               ->innerJoinWith('purchase')
               ->andWhere(['between','dtb_purchase.create_date',$this->start_date, $this->end_date])
               ->andWhere(['dtb_purchase.status' => PurchaseStatus::PKEY_RETURN])
               ->andWhere(['<>', 't.minus_product', 1])
               ->andWhere('t.code like :query', [':query' => \common\models\Vegetable::EAN13_PREFIX . '0%'])
               ->select([
                   'SUM(ABS(t.quantity)) as quantity',
                   'ABS(SUM(t.quantity * t.unit_price)) as basePrice',
                   'dtb_purchase.branch_id',
                   '(SELECT 99) AS category_id',
                   '(SELECT company_id FROM mtb_branch WHERE branch_id = dtb_purchase.branch_id) as company_id',
               ])
              ->groupBy(['company_id', 'dtb_purchase.branch_id', 'category_id'])
              ->orderBy(['company_id' => SORT_ASC, 'dtb_purchase.branch_id' => SORT_ASC, 'category_id' => SORT_ASC]);

        $query3 = PurchaseItem::find()
               ->from(PurchaseItem::tableName() . ' t')
               ->innerJoinWith('purchase')
               ->andWhere(['between','dtb_purchase.create_date',$this->start_date, $this->end_date])
               ->andWhere(['dtb_purchase.status' => PurchaseStatus::PKEY_RETURN])
               ->andWhere(['<>', 't.minus_product', 1])
               ->andWhere('t.code not like :query', [':query' => \common\models\Vegetable::EAN13_PREFIX . '0%'])
               ->andWhere('t.product_id IS NULL')
               ->select([
                   'SUM(ABS(t.quantity)) as quantity',
                   'ABS(SUM(t.quantity * t.unit_price)) as basePrice',
                   'dtb_purchase.branch_id',
                   '(SELECT CASE WHEN vial_id IN (5, 6, 7, 8, 9, 11, 12) THEN 98 ELSE 6 END FROM mvtb_product_master WHERE ean13 = t.code'
                       . ' UNION ALL SELECT DISTINCT 6 FROM mvtb_product_master'
                       . ' WHERE NOT EXISTS (SELECT * FROM mvtb_product_master WHERE ean13 = t.code)) AS category_id',
                   '(SELECT CASE '
                       . 'WHEN dtb_purchase.branch_id = 6 THEN t.company_id '
                       . 'WHEN category_id = 3 THEN 4 '
                       . 'ELSE company_id END FROM mtb_branch WHERE branch_id = dtb_purchase.branch_id) as company_id',
               ])
              ->groupBy(['company_id', 'dtb_purchase.branch_id', 'category_id'])
              ->orderBy(['company_id' => SORT_ASC, 'dtb_purchase.branch_id' => SORT_ASC, 'category_id' => SORT_ASC]);

        if(!is_null($this->branch_id) && $this->branch_id !== '99'){
            self::narrowQuery($query, $this->branch_id);
            self::narrowQuery($query2, $this->branch_id);
            self::narrowQuery($query3, $this->branch_id);
        }

        if($this->company_id){
            $query->andWhere(['(SELECT CASE '
                                . 'WHEN dtb_purchase.branch_id = 6 THEN t.company_id '
                                . 'WHEN (SELECT CASE WHEN p.liquor_flg = 1 THEN 98 WHEN code LIKE "231%" THEN 5 ELSE c.category_id END FROM mtb_category c INNER JOIN dtb_product p ON p.category_id = c.category_id WHERE p.product_id = t.product_id) = 3 THEN 4 '
                                . 'ELSE company_id END '
                                . 'FROM mtb_branch WHERE branch_id = dtb_purchase.branch_id)' => $this->company_id]);
            $query2->andWhere(['(SELECT company_id FROM mtb_branch WHERE branch_id = dtb_purchase.branch_id)' => $this->company_id]);
            $query3->andWhere(['(SELECT CASE '
                                . 'WHEN dtb_purchase.branch_id = 6 THEN t.company_id '
                                . 'ELSE company_id END '
                                . 'FROM mtb_branch WHERE branch_id = dtb_purchase.branch_id)' => $this->company_id]);
        }

        if ($this->payment_id) {
            $query->andWhere(['dtb_purchase.payment_id' => $this->payment_id]);
            $query2->andWhere(['dtb_purchase.payment_id' => $this->payment_id]);
            $query3->andWhere(['dtb_purchase.payment_id' => $this->payment_id]);
        }

        if($this->class) {
            if($this->class == 0) {
            }

            if($this->class == 1) {
                $query->innerJoinWith('stocks'); // 酒類判定のため

                $query->andWhere(['not',['t.remedy_id' => 0]]);
                $query->andWhere(['in', 'vial_id', [RemedyVial::GLASS_5ML,
                    RemedyVial::GLASS_SPRAY_10ML,
                    RemedyVial::GLASS_20ML,
                    RemedyVial::GLASS_150ML,
                    RemedyVial::GLASS_720ML,
                    RemedyVial::PLASTIC_SPRAY_100ML,
                    RemedyVial::PLASTIC_SPRAY_50ML]]);

            }
            if($this->class == 2) {
                $query->innerJoinWith('product'); // レストラン判定のため
                $query->andWhere(['category_id' => Category::RESTAURANT]);
            }

            $query->groupBy('t.code');
            return $query;
        }
        $query->union($query2);
        $query->union($query3);
        return $query;
    }

    /**
     * 値引き商品を集計
     * @return type
     */
    public function getMinusItemQuery()
    {
        // 野菜以外の集計を取得
        $query = PurchaseItem::find()
               ->from(PurchaseItem::tableName() . ' t')
               ->innerJoinWith('purchase')
               ->andWhere(['between','dtb_purchase.create_date',$this->start_date, $this->end_date])
               ->andWhere(['between','dtb_purchase.status',PurchaseStatus::PKEY_INIT, PurchaseStatus::PKEY_DONE])
               ->andWhere(['t.minus_product' => 1])
               ->andWhere('t.code not like :query', [':query' => \common\models\Vegetable::EAN13_PREFIX . '0%'])
               ->andWhere('t.product_id IS NOT NULL')
               ->select([
                   'SUM(t.quantity) as quantity',
                   'SUM(t.quantity * ABS(t.unit_price)) as basePrice',
                   'dtb_purchase.branch_id',
                   '(SELECT CASE WHEN p.liquor_flg = 1 THEN 98 WHEN code LIKE "231%" THEN 5 ELSE c.category_id END FROM mtb_category c INNER JOIN dtb_product p ON p.category_id = c.category_id WHERE p.product_id = t.product_id) AS category_id',
                   '(SELECT CASE '
                       . 'WHEN dtb_purchase.branch_id = 6 THEN t.company_id '
                       . 'WHEN category_id = 3 THEN 4 '
                       . 'ELSE company_id END '
                       . 'FROM mtb_branch WHERE branch_id = dtb_purchase.branch_id) as company_id',
               ])
              ->groupBy(['company_id', 'dtb_purchase.branch_id', 'category_id'])
              ->orderBy(['company_id' => SORT_ASC, 'dtb_purchase.branch_id' => SORT_ASC, 'category_id' => SORT_ASC]);

        // 野菜の集計を取得。先頭が２３で、７桁目まで（２３＋野菜ID）の集計
        $query2 = PurchaseItem::find()
               ->from(PurchaseItem::tableName() . ' t')
               ->innerJoinWith('purchase')
               ->andWhere(['between','dtb_purchase.create_date',$this->start_date, $this->end_date])
               ->andWhere(['between','dtb_purchase.status',PurchaseStatus::PKEY_INIT, PurchaseStatus::PKEY_DONE])
               ->andWhere(['t.minus_product' => 1])
               ->andWhere('t.code like :query', [':query' => \common\models\Vegetable::EAN13_PREFIX . '0%'])
               ->select([
                   'SUM(quantity) as quantity',
                   'SUM(quantity * ABS(t.unit_price)) as basePrice',
                   'dtb_purchase.branch_id',
                   '(SELECT 99) AS category_id',
                   '(SELECT company_id FROM mtb_branch WHERE branch_id = dtb_purchase.branch_id) as company_id',
               ])
              ->groupBy(['company_id', 'dtb_purchase.branch_id', 'category_id'])
              ->orderBy(['company_id' => SORT_ASC, 'dtb_purchase.branch_id' => SORT_ASC, 'category_id' => SORT_ASC]);

        $query3 = PurchaseItem::find()
               ->from(PurchaseItem::tableName() . ' t')
               ->innerJoinWith('purchase')
               ->andWhere(['between','dtb_purchase.create_date',$this->start_date, $this->end_date])
               ->andWhere(['between','dtb_purchase.status',PurchaseStatus::PKEY_INIT, PurchaseStatus::PKEY_DONE])
               ->andWhere(['t.minus_product' => 1])
               ->andWhere('t.code not like :query', [':query' => \common\models\Vegetable::EAN13_PREFIX . '0%'])
               ->andWhere('t.product_id IS NULL')
               ->select([
                   'SUM(t.quantity) as quantity',
                   'SUM(t.quantity * t.unit_price) as basePrice',
                   'dtb_purchase.branch_id',
                   '(SELECT CASE WHEN vial_id IN (5, 6, 7, 8, 9, 11, 12) THEN 98 ELSE 6 END FROM mvtb_product_master WHERE ean13 = t.code'
                       . ' UNION ALL SELECT DISTINCT 6 FROM mvtb_product_master'
                       . ' WHERE NOT EXISTS (SELECT * FROM mvtb_product_master WHERE ean13 = t.code)) AS category_id',
                   '(SELECT CASE '
                       . 'WHEN dtb_purchase.branch_id = 6 THEN t.company_id '
                       . 'WHEN category_id = 3 THEN 4 '
                       . 'ELSE company_id END FROM mtb_branch WHERE branch_id = dtb_purchase.branch_id) as company_id',
               ])
              ->groupBy(['company_id', 'dtb_purchase.branch_id', 'category_id'])
              ->orderBy(['company_id' => SORT_ASC, 'dtb_purchase.branch_id' => SORT_ASC, 'category_id' => SORT_ASC]);

        if(!is_null($this->branch_id) && $this->branch_id !== '99'){
            self::narrowQuery($query, $this->branch_id);
            self::narrowQuery($query2, $this->branch_id);
            self::narrowQuery($query3, $this->branch_id);
        }

        if($this->company_id){
            $query->andWhere(['(SELECT CASE '
                                . 'WHEN dtb_purchase.branch_id = 6 THEN t.company_id '
                                . 'WHEN (SELECT CASE WHEN p.liquor_flg = 1 THEN 98 WHEN code LIKE "231%" THEN 5 ELSE c.category_id END FROM mtb_category c INNER JOIN dtb_product p ON p.category_id = c.category_id WHERE p.product_id = t.product_id) = 3 THEN 4 '
                                . 'ELSE company_id END '
                                . 'FROM mtb_branch WHERE branch_id = dtb_purchase.branch_id)' => $this->company_id]);
            $query2->andWhere(['(SELECT company_id FROM mtb_branch WHERE branch_id = dtb_purchase.branch_id)' => $this->company_id]);
            $query3->andWhere(['(SELECT CASE '
                                . 'WHEN dtb_purchase.branch_id = 6 THEN t.company_id '
                                . 'ELSE company_id END '
                                . 'FROM mtb_branch WHERE branch_id = dtb_purchase.branch_id)' => $this->company_id]);
        }

        if ($this->payment_id) {
            $query->andWhere(['dtb_purchase.payment_id' => $this->payment_id]);
            $query2->andWhere(['dtb_purchase.payment_id' => $this->payment_id]);
            $query3->andWhere(['dtb_purchase.payment_id' => $this->payment_id]);
        }

        if($this->class) {
            if($this->class == 0) {
            }

            if($this->class == 1) {
                $query->innerJoinWith('stocks'); // 酒類判定のため

                $query->andWhere(['not',['t.remedy_id' => 0]]);
                $query->andWhere(['in', 'vial_id', [RemedyVial::GLASS_5ML,
                    RemedyVial::GLASS_SPRAY_10ML,
                    RemedyVial::GLASS_20ML,
                    RemedyVial::GLASS_150ML,
                    RemedyVial::GLASS_720ML,
                    RemedyVial::PLASTIC_SPRAY_100ML,
                    RemedyVial::PLASTIC_SPRAY_50ML]]);

            }
            if($this->class == 2) {
                $query->innerJoinWith('product'); // レストラン判定のため
                $query->andWhere(['category_id' => Category::RESTAURANT]);
            }

            $query->groupBy('t.code');
            return $query;
        }
        $query->union($query2);
        $query->union($query3);
        return $query;
    }
    

    public function getDiscount()
    {
        $value = (int) $this->query->sum('discount');

        return $this->company_id ? (float) ($value * $this->share) : $value;
    }

    public function getHandling()
    {
        $value = (int) $this->query->sum('handling');

        return $this->company_id ? (float) ($value * $this->share) : $value;
    }

    public function getItemCount()
    {
        return (int) $this->itemQuery->sum('quantity') - (int) $this->returnItemQuery->sum('quantity');
    }

    public function getItemProvider()
    {
        return new \yii\data\SqlDataProvider([
            'sql'        => $this->itemQuery->createCommand()->sql,
            'params'     => $this->itemQuery->createCommand()->params,
            'totalCount' => $this->itemQuery->count(),
            'pagination' => false,
        ]);
    }

    public function getReturnItemProvider()
    {
        return new \yii\data\SqlDataProvider([
            'sql'        => $this->returnItemQuery->createCommand()->sql,
            'params'     => $this->returnItemQuery->createCommand()->params,
            'totalCount' => $this->returnItemQuery->count(),
            'pagination' => false,
        ]);
    }

    public function getMinusItemProvider()
    {
        return new \yii\data\SqlDataProvider([
            'sql'        => $this->minusItemQuery->createCommand()->sql,
            'params'     => $this->minusItemQuery->createCommand()->params,
            'totalCount' => $this->minusItemQuery->count(),
            'pagination' => false,
        ]);
    }
    
    public function getHeaderItemProvider()
    {
        return new \yii\data\SqlDataProvider([
            'sql'        => $this->headerItemQuery->createCommand()->sql,
            'params'     => $this->headerItemQuery->createCommand()->params,
            'totalCount' => $this->headerItemQuery->count(),
            'pagination' => false,
        ]);
    }

    public function getPointProvider()
    {
        return new \yii\data\SqlDataProvider([
            'sql'        => $this->pointQuery->createCommand()->sql,
            'params'     => $this->pointQuery->createCommand()->params,
            'totalCount' => $this->pointQuery->count(),
            'pagination' => false,
        ]);
    }

    public function getDetailItemProvider()
    {
        return new \yii\data\SqlDataProvider([
            'sql'        => $this->detailItemQuery->createCommand()->sql,
            'params'     => $this->detailItemQuery->createCommand()->params,
            'totalCount' => $this->detailItemQuery->count(),
            'sort' => [
                'attributes' => [
                    'category',
                    'price',
                    'name' => [
                        'asc'     => ['first_name' => SORT_ASC, 'last_name' => SORT_ASC],
                        'desc'    => ['first_name' => SORT_DESC, 'last_name' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label'   => 'Name',
                    ],
                    'quantity',
                    'basePrice',
                    'discountTotal',
                    'pointTotal',
                ],
                'defaultOrder' => ['category'=>SORT_DESC],
            ],
            'pagination' => false,
        ]);
    }

    public function getPointConsume()
    {
        $value = (int) $this->query->sum('point_consume');

        return $this->company_id ? (float) ($value * $this->share) : $value;
    }

    public function getPostage()
    {
        $value = (int) $this->query->sum('postage');

        return $this->company_id ? (float) ($value * $this->share) : $value;
    }

    public function getPurchaseCount()
    {
        return $this->headerItemQuery->count();
    }

    /**
     * 対象年月日におけるその会社の商品が占める割合を商品小計を元にして求める
     * @return (int or float)
     */
    public function getShare()
    {
        if(! $this->company_id)
            return 1; // 100 %

        if (!(int) $this->query->sum('subtotal')) {
            return 0;
        }

        return ($this->getSubtotal() / (int) $this->query->sum('subtotal'));
    }

    public function getSubtotal()
    {
        if(! $this->company_id)
            return (int) $this->query->sum('subtotal');

        return (int) $this->getItemQuery()->sum('basePrice');
    }

    public function getTax()
    {
        $value = (int) $this->query->sum('tax');

        return $this->company_id ? (float) ($value * $this->share) : $value;
    }

    public function getTotalCharge()
    {
        $value = (int) $this->query->sum('total_charge');

        return $this->company_id ? (float) ($value * $this->share) : $value;
    }

    private static function narrowQuery($query, $branch_id)
    {
        $query->andWhere(['dtb_purchase.branch_id' => $branch_id]);
    }

}
