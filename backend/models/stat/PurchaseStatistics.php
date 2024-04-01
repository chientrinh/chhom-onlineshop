<?php
namespace backend\models\stat;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/stat/PurchaseStatistics.php $
 * $Id: PurchaseStatistics.php 2857 2016-08-19 08:54:36Z mori $
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

class PurchaseStatistics extends \yii\base\Model
{
    public $start_date;
    public $end_date;
    public $company_id;
    public $branch_id;
    public $class; // 分類

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
            'agent_id'  => "サポート申込ID",
        ];
    }

    public function getQuery()
    {
        $query = Purchase::find()->active()
                                 ->andWhere(['between', 'create_date', $this->start_date, $this->end_date]);

        if($this->branch_id)
            self::narrowQuery($query, $this->branch_id);

        return $query;
    }
    
    public function getHeaderItemQuery()
    {
        $query = $this->query;    
        if($this->company_id)
            $query->andWhere(['company_id' => $this->company_id]);
        return $query;
    }

    /**
     * ライブ配信チケットのデータを検索する
     * branch_id:16固定、伝票ステータス キャンセル未満
     */
    public function getLiveDataItemQuery()
    {
        $query = PurchaseItem::find()
               ->from(PurchaseItem::tableName() . ' t')
               ->joinWith('product')
               ->innerJoinWith('purchase')
               ->leftJoin(['customer' => \common\models\Customer::tableName()], 'customer.customer_id=`dtb_purchase`.`customer_id`')
               ->leftJoin(['grade' => \common\models\CustomerGrade::tableName()], 'customer.grade_id=grade.grade_id')
               ->innerJoin(['delivery' => \common\models\PurchaseDelivery::tableName()], 'delivery.purchase_id=t.purchase_id')
               ->andWhere(['between','dtb_purchase.create_date',$this->start_date, $this->end_date])
            //    ->andWhere(['>=','dtb_purchase.create_date',$this->start_date])
               ->andWhere(['<','dtb_purchase.status',PurchaseStatus::PKEY_CANCEL])
               ->andWhere(['not', ['t.product_id' => null]])
               ->andWhere(['branch_id' => 16])
               ->select([
                   'dtb_purchase.purchase_id AS purchase_id',
                   'dtb_purchase.branch_id AS branch_id',
                   'dtb_purchase.create_date',
                   'dtb_purchase.customer_id AS customer_id',
                   'grade.name AS customer_grade',
                   'CONCAT(delivery.name01," ",delivery.name02) AS delivery_customer_name',
                   'CONCAT(customer.name01," ",customer.name02) AS customer_name',
                   'CONCAT(customer.kana01," ",customer.kana02) AS customer_kana',
                   'dtb_purchase.email AS email',
                   'CONCAT(delivery.tel01,"-",delivery.tel02,"-",delivery.tel03) AS tel',
                   't.product_id AS product_id',
                   't.name AS product_name',
                   't.unit_price',
                   't.unit_tax',
                   'quantity',
                   '(quantity * (t.unit_price + t.unit_tax)) as basePrice',
                   'dtb_purchase.payment_id AS payment_id',
                   'dtb_purchase.branch_id AS branch_id',
                   'dtb_purchase.status AS status',
                   'dtb_purchase.note AS note',
                   'dtb_purchase.customer_msg AS customer_msg',
                   'dtb_purchase.agent_id AS agent_id'
               ]);
        
        return $query;
    }

    
    public function getDetailItemQuery()
    {
        $product_query = PurchaseItem::find()
               ->from(PurchaseItem::tableName() . ' t')
               ->joinWith('product')
               ->innerJoinWith('purchase')
               ->andWhere(['between','dtb_purchase.create_date',$this->start_date, $this->end_date])
               ->andWhere(['between','dtb_purchase.status',PurchaseStatus::PKEY_INIT, PurchaseStatus::PKEY_DONE])
               ->andWhere(['not', ['t.product_id' => null]])
               ->select([
                   'dtb_product.product_id',
		   't.remedy_id',
                   'dtb_product.category_id',
                   't.name',
                   't.code',
                   't.price',
                   'quantity',
                   '(quantity * t.price) as basePrice',
                   '(quantity * floor(t.price * point_rate    /100)) as pointTotal',
                   '(quantity * discount_amount) as discountTotal',
                   't.company_id',
                   't.is_wholesale',
               ]);
        
        $remedy_query = PurchaseItem::find()
               ->from(PurchaseItem::tableName() . ' t')
               ->joinWith('product')
               ->innerJoinWith('purchase')
               ->andWhere(['between','dtb_purchase.create_date',$this->start_date, $this->end_date])
               ->andWhere(['between','dtb_purchase.status',PurchaseStatus::PKEY_INIT, PurchaseStatus::PKEY_DONE])
               ->andWhere(['t.product_id' => null])
               ->select([
                   'dtb_product.product_id',
		   't.remedy_id',
                   '(6) as category_id',
                   't.name',
                   't.code',
                   't.price',
                   'quantity',
                   '(quantity * t.price) as basePrice',
                   '(quantity * floor(t.price * point_rate    /100)) as pointTotal',
                   '(quantity * discount_amount) as discountTotal',
                   't.company_id',
                   't.is_wholesale',
               ]);
    
        $query = $product_query->union($remedy_query);
            
                
        if($this->branch_id)
            self::narrowQuery($query, $this->branch_id);

        if($this->company_id)
            $query->andWhere(['t.company_id' => $this->company_id]);
        return $query;
    }
    
    public function getItemQuery()
    {
// 野菜以外の集計を取得
        $query = PurchaseItem::find()
               ->from(PurchaseItem::tableName() . ' t')
               ->innerJoinWith('purchase')
//               ->innerJoinWith('stocks') // 酒類判定のため
//               ->innerJoinWith('product') // レストラン判定のため
               ->andWhere(['between','dtb_purchase.create_date',$this->start_date, $this->end_date])
               ->andWhere(['between','dtb_purchase.status',PurchaseStatus::PKEY_INIT, PurchaseStatus::PKEY_DONE])
               ->andWhere('t.code not like :query', [':query' => \common\models\Vegetable::EAN13_PREFIX.'%'])
               ->select([
                   't.product_id',
		           't.remedy_id',
                   't.name',
                   't.code',
                   't.price','SUM(quantity) as quantity',
                   'SUM(quantity * t.price) as basePrice',
                   'SUM(quantity * floor(t.price * point_rate    /100)) as pointTotal',
                   'SUM(quantity * discount_amount) as discountTotal',
                   't.company_id',
                   't.is_wholesale',
                   't.unit_price',
                   't.unit_tax',
               ]);
//               ->groupBy('t.code');

// 野菜の集計を取得。先頭が２３で、７桁目まで（２３＋野菜ID）の集計
        $query2 = PurchaseItem::find()
               ->from(PurchaseItem::tableName() . ' t')
               ->innerJoinWith('purchase')
               ->andWhere(['between','dtb_purchase.create_date',$this->start_date, $this->end_date])
               ->andWhere(['between','dtb_purchase.status',PurchaseStatus::PKEY_INIT, PurchaseStatus::PKEY_DONE])
               ->andWhere('code like :query', [':query' => \common\models\Vegetable::EAN13_PREFIX.'%'])
               ->select([
                   'product_id',
                   'remedy_id',
                   'name',
                   'code',
                   'price','SUM(quantity) as quantity',
                   'SUM(quantity * price) as basePrice',
                   'SUM(quantity * floor(price * point_rate    /100)) as pointTotal',
                   'SUM(quantity * discount_amount) as discountTotal',
                   't.company_id',
                   't.is_wholesale',
                   't.unit_price',
                   't.unit_tax',
               ])
	       ->groupBy(['SUBSTR(code,1,7)']);
        $code = 'SUBSTR(code,1,7)';


        if($this->branch_id){
            self::narrowQuery($query, $this->branch_id);
            self::narrowQuery($query2, $this->branch_id);
        }

        if($this->company_id){
            $query->andWhere(['t.company_id' => $this->company_id]);
            $query2->andWhere(['t.company_id' => $this->company_id]);
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
//                $query->andFilterWhere(["or", ['like', 'name', 'ランチ'],['like', 'name', 'ディナー']]);
            }

            $query->groupBy('t.code');
//var_dump($this->class);           
//print_r($query->createCommand()->rawSql);exit;
            return $query;
        }
        $query->groupBy('t.code');

        $query->union($query2);

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
//        return (int) $this->detailItemQuery->sum('quantity');
        return (int) $this->itemQuery->sum('quantity');
    }

    public function getItemProvider()
    {
        return new \yii\data\SqlDataProvider([
            'sql'        => $this->itemQuery->createCommand()->sql,
            'params'     => $this->itemQuery->createCommand()->params,
            'totalCount' => $this->itemQuery->count(),
            'sort' => [
                'attributes' => [
                    'code',
                    'product_id',
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
                'defaultOrder' => ['quantity'=>SORT_DESC],
            ],
            'pagination' => false,
        ]);
    }

    public function getLiveDataItemProvider()
    {
        return new \yii\data\SqlDataProvider([
            'sql'        => $this->liveDataItemQuery->createCommand()->sql,
            'params'     => $this->liveDataItemQuery->createCommand()->params,
            'totalCount' => $this->liveDataItemQuery->count(),
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
        return $this->query->count();
    }

    /**
     * 対象年月日におけるその会社の商品が占める割合を商品小計を元にして求める
     * @return (int or float)
     */
    public function getShare()
    {
        if(! $this->company_id)
            return 1; // 100 %

        return @($this->getSubtotal() / (int) $this->query->sum('subtotal'));
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
