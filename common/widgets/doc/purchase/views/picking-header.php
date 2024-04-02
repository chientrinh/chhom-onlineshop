<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\models\CustomerInfo;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/views/picking-header.php $
 * $Id: picking-header.php 3956 2018-07-05 04:27:06Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Purchase
 */
?>

<style type="text/css">
thead { display: table-header-group }
tfoot { display: table-row-group }
tr { page-break-inside: avoid }

body {
    lang: ja;
    font-family: ipagothic;
}

h1 { width:100%; text-align:center; font-size: 12pt; margin-top:5px; margin-bottom:5; }

p, th, td
{
    font-size: 10 pt;
}

.text-center { text-align:center }
.text-right  { text-align:right }
.text-left   { text-align:left }
.product-name {
    padding-left: 3mm;
    font-size: 10pt;
}
table
{
    width:  100%;
    vertical-align:top;
    border-collapse:collapse;
    cellspacing:0;
}
#delivery-item tr,
#piking-item tr,
#piking-item td
{
    border: 1px solid lightgray;
}
#delivery-item th,
#piking-item th
{
    background: gainsboro;
}
div
{
    border: 1px red;
}
td.transparent
{
    border: solid 0px;
}
</style>

<div id="picking-<?= $model->primaryKey ?>" style="padding:0; margin-bottom:5;">
    <h1>
        <?= $title ?>
    </h1>
<?php if(! $model->delivery): ?>
    <p class="alert alert-warning">この注文には配達先がありません</p>
<?php endif ?>
    <p class="text-right"><?= $model->shipping_date ? date('Y 年 m 月 d 日', strtotime($model->shipping_date)) : '　　 年 　 月 　 日' ?></p>
    <p class="text-right">受付 : <?= $model->creator ? $model->creator->name01 : "WEB" ?></p>
    <p class="text-right">支払 : <?= $model->payment->name ?> </p>

    <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'id' => 'picking-detail',
        'options' => ['class'=>'picking-detail'],
        'attributes' => [
            [
                'attribute' => 'purchase_id',
                'value'     => sprintf('%06d', $model->purchase_id),
            ],
            [
                'label'     => "注文日時",
                'attribute' => 'create_date',
                'format'    => ['date','php:Y-m-d H:i'],
            ],
            [
                'label'  => "注文者",
                'format' => 'raw',
                'value'  => ($c = $model->customer)
                    ? sprintf("%s (%d)(%s) %s",
                              $c->name,
                              $c->customer_id,
                              mb_substr(ArrayHelper::getValue($c->grade, 'nickname'),0,1),
                              (($mships = $c->memberships) ? implode(', ', ArrayHelper::getColumn($mships,'membership.name')) : null)
                ) : null,
            ],
            [
                'label' => "配達指定",
                'value' => ($model->hasAttribute('purchase_id') && $model->delivery)
                       ? $model->delivery->datetimeString
                       : null,
            ],
            [
                'label'   => "お届け先",
                'visible' => $model->delivery,
                'format'  => 'raw',
                'value'   => $model->delivery ? sprintf('<p> 〒%s <br> %s <br> %s <br> %s </p>', $model->delivery->zip, $model->delivery->addr, $model->delivery->name, $model->delivery->tel) : null,
                'options' => ['style'=>'text-align:right'],
            ],
            [
                'attribute' => 'delivery.gift',
                'value'     => $model->isGift() ? '非表示' : '表示',
            ],
            [
                'attribute' => 'subtotal',
                'format'    => 'currency',
            ],
            [
                'attribute' => 'tax',
                'format'    => 'currency',
            ],
            [
                'attribute' => 'point_consume',
                'format'    => 'currency',
            ],
            [
                'attribute' => 'discount',
                'format'    => 'currency',
            ],
            [
                'attribute' => 'postage',
                'format'    => 'currency',
            ],
            [
                'attribute' => 'handling',
                'format'    => 'currency',
            ],
            [
                'attribute' => 'total_charge',
                'format'    => 'currency',
            ],

            'customer_msg',
            'note',
        ],
    ]) ?>

<!--<?php if($model->shipped): ?>
    <h1 style="background-color:yellow">
        注意：この注文は配送済みに設定されています。
        最終更新日時：<?= $model->update_date ?>
    </h1>
<?php endif ?>-->

<?php $query = CustomerInfo::find()->andwhere(['customer_id' => $model->customer_id])
                                   ->andWhere(['>=', 'weight_id', 3 /* 警報 */]); ?>
<?php if($query->exists()): ?>
    <div style="background-color:#99ff99">
        <?= \yii\grid\GridView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query'      => $query,
                'pagination' => false,
            ]),
            'showHeader' => false,
            'layout'     => '{items}',
            'columns'    => ['content'],
        ]) ?>
    </div>
<?php endif ?>

</div>
