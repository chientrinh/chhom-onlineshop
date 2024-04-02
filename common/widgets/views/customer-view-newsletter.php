<?php
/**
 * $URL: https://localhost:44344/svn/MALL/common/widgets/views/customer-view-memberships.php $
 * $Id: customer-view-memberships.php 1753 2015-11-03 01:33:20Z mori $
 *
 * @var $this  yii/web/View
 * @var $model common/models/Customer
 */

use \yii\helpers\ArrayHelper;
use \yii\helpers\Html;
use \common\models\Pointing;
use \common\models\PointingItem;
use \common\models\Product;
use \common\models\Purchase;
use \common\models\PurchaseItem;

if($model->parent)
    $customer_id = $model->parent->customer_id;
else
    $customer_id = $model->customer_id;

$query = PurchaseItem::find()
       ->where(['product_id' => Product::find()->oasis()->select(['product_id'])])
       ->joinWith(['purchase', 'purchase.branch'], false)
       ->andWhere(['dtb_purchase.customer_id' => $customer_id])
       ->select(['dtb_purchase.create_date as date','product_id','dtb_purchase_item.name','mtb_branch.name as sender'])
       ->asArray();

$query->union(
         PointingItem::find()
       ->where(['product_id' => Product::find()->oasis()->select(['product_id'])])
       ->innerJoinWith('pointing', true)
       ->andWhere(['dtb_pointing.customer_id' => $customer_id])
       ->andWhere(['dtb_pointing.status' => Pointing::STATUS_SOLD])
       ->select(['dtb_pointing.create_date as date','product_id','name', '(select "代理店")'])
       ->asArray()
);

$items = $query->createCommand()->queryAll();

$notSent = Product::find()
         ->where(['like','name','Oasis'])
         ->andWhere(['not',['product_id'=> ArrayHelper::getColumn($items,'product_id')]])
         ->all();
?>

<div class="col-md-12">
    <h3>
        <small>
            とらのこ会報誌『オアシス』の発送履歴
        </small>
    </h3>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => $items,
        ]),
        'layout'  => '{items}{pager}',
        'emptyText' => 'まだありません',
        'showOnEmpty' => false,
        'columns' => [
            'name',
            [
                'label'     => '発送日',
                'attribute' => 'date',
                'format'    => ['date','php:Y-m-d'],
            ],
            [
                'label'     => '発送者',
                'attribute' => 'sender',
                'visible'   => $backend,
            ],
        ],
    ]); ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => $notSent,
        ]),
        'layout'  => '{items}{pager}',
        'caption'   => '未発送の会報誌',
        'emptyText' => '未発送の会報誌はありません',
        'showOnEmpty' => false,
        'showHeader'  => false,
        'columns' => [
            [
                'attribute' => 'name',
            ],
            [
                'label' => '',
                'format'=> 'html',
                'value' => function($data)use($customer_id){ return Html::a('発送済みにする',['oasis/mark-as-shipped','pid'=>$data->product_id,'cid'=>$customer_id],['class'=>'']); },
            ],

        ],
    ]); ?>

</div>

