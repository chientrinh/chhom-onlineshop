<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/views/delivery.php $
 * $Id: delivery.php 4185 2019-09-30 16:12:44Z mori $
 *
 * @var $this    yii\web\View
 * @var $model   common\models\Purchase
 * @var $company common\models\Company
 * @var $items   common\models\PurchaseItem[]
 * @var $summaryColumns array or null represents DetailView::attributes
 */

$formatter = Yii::$app->formatter;
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

<h1><?= $title ?></h1>

<div>
    <p class="text-right"><?= $model->shipping_date ? date('Y 年 m 月 d 日', strtotime($model->shipping_date)) : '　　 年 　 月 　 日' ?></p>

    <div style="float:left;width:60%;height:36mm">
      <p>［お届け先］<br>
        <?php if(! $delivery = $model->delivery){ $delivery = $model->customer; } ?>
        <?php if(! $delivery): ?>
            指定がありません
        <?php else: ?>
            〒<?= $delivery->zip ?><br>
            <?= $delivery->addr ?><br>
            <?= $delivery->name ?>様<br>
            TEL: <?= $delivery->tel ?>
        <?php endif ?></p>
    </div>

    <?php if (isset($target) && $target === 'sodan'):?>
        <div style="float:right;width:40%;height:36mm">
          <p>［販売者］<br>
            <strong><?= $model->branch->name ?></strong><br>
            〒<?= $model->branch->zip ?><br>
            <?= $model->branch->addr ?><br>
        </div>
    <?php else: ?>
        <div style="float:right;width:40%;height:36mm">
          <p>［販売者］<br>
            <strong><?= $company->name ?></strong><br>
            〒<?= $company->zip ?><br>
            <?= $company->addr ?><br>

         <?php if(common\models\Company::PKEY_HE == $company->company_id): ?>
            <p>［商品に関する問い合わせ］<br>
            TEL: 0557-86-3075</p>
         <?php else: ?>
            TEL: <?= $company->tel ?></p>
         <?php endif ?>
        </div>
    <?php endif;?>

    <div style="float:none;width:100%">
      <p>
        このたびはお買い上げいただきありがとうございます。<br>
        下記の内容にて納品させていただきます。<br>
        ご確認いただきますよう、お願いいたします。
      </p>
    </div>

    <div class="wrap" style="display:block;width:100%;height:32mm;">
      <div style="float:left;width:60%">
        <p>[ご注文者]<br>
          <?php if($model->customer_id): ?>
          〒<?= $model->customer->zip ?><br>
          <?= $model->customer->addr ?><br>
          <?= $model->customer->name ?><br>
          TEL: <?= $model->customer->tel ?><br><br>
          【会員番号：<?= $model->customer->code ?>】
          <?php endif ?>
        </p>
      </div>

      <div style="float:left;width:15%">
        <p>
          [注文番号]<br>
          [注文日時]
        </p>
      </div>
      <div style="float:right;width:25%" class="text-right">
        <p>
          <?= sprintf('%06d',$model->primaryKey) ?><br>
          <?= date('Y 年 m 月 d 日 H:i', strtotime($model->create_date)) ?>
        </p>
      </div>
      <div style="float:right;width:40%" class="text-right">
    <?php
    /* if(strtotime($model->create_date) >= \common\models\Tax::newDate()) {
        $list= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels'  => $taxSummaryColumns,
            'pagination' => false,
        ]),
        'id'         => 'delivery-tax-summary',
        'tableOptions' => ['class'=>'grid-view', 'style' => 'border: solid 0.5px #000000; border-collapse: collapse;'],
        'layout'=>'{items}',
        'columns' => [
            [
                'attribute' => 'tax_rate',
                'label'     => '税率',
                'format'    => 'html',
                'value'     => function($data)use($formatter) { return $data['tax_rate'] < 999 ? $formatter->asPercent($data['tax_rate'] / 100) : '計' },
                'contentOptions' => ['style'=>'width:20%; border: solid 0.5px #000000; border-collapse: collapse;', 'class'=>'text-center'],
                'headerOptions'  => ['class'=>'text-center', 'style' => 'border: solid 0.5px #000000; border-collapse: collapse;'],

            ],
            [
                'attribute' => 'subtotal',
                'label'     => '税別金額',
                'format'    => 'currency',
                'contentOptions' => ['style'=>'width:40%; border: solid 0.5px #000000; border-collapse: collapse;', 'class'=>'text-right'],
                'headerOptions'  => ['class'=>'text-center', 'style' => 'border: solid 0.5px #000000; border-collapse: collapse;'],
            ],
            [
                'attribute' => 'tax',
                'label'     => '消費税',
                'format'    => 'currency',
                'contentOptions' => ['style'=>'width:40%; border: solid 0.5px #000000; border-collapse: collapse;', 'class'=>'text-right'],
                'headerOptions'  => ['class'=>'text-center', 'style' => 'border: solid 0.5px #000000; border-collapse: collapse;'],
            ],
        ]
    ]); echo $list;
      }*/ ?>
      </div>
    </div>

    <div class="wrap" style="float:none;width:100%">

      <p>[明細]</p>

<?= \yii\grid\GridView::widget([
    'dataProvider' => new \yii\data\ArrayDataProvider([
        'allModels'  => $items,
        'pagination' => false,
    ]),
    'id'=>'delivery-item',
    'tableOptions' => ['class'=>'grid-view'],
    'layout'=>'{items}',
    'columns' => [
        [
            'attribute' => 'code',
            'contentOptions' => ['style'=>'width:12%'],
            'headerOptions'  => ['class'=>'text-left'],
        ],
        [
            'attribute' => 'name',
            'value'     => function($data) use ($model) {
                return strtotime($model->create_date) >= \common\models\Tax::newDate() && $data->isReducedTax() ? $data->name.'※' : $data->name;
            },
            'contentOptions' => ['style'=>'width:43%'],
            'headerOptions'  => ['class'=>'text-left'],
        ],
        [
            'attribute' => 'price',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right',
                                 'style'=>'width:12%'],
            'headerOptions'  => ['class'=>'text-right'],
            'visible'   => ! $model->isGift(),
        ],
        [
            'label'     => 'ご優待',
            'attribute' => 'discount_rate',
            'format'    => 'html',
            'value'     => function($data)use($formatter){
                if($data->discount_rate)
                    return '&minus;' . $formatter->asPercent($data->discount_rate / 100);

                if($data->discount_amount)
                    return '&minus;' . $formatter->asCurrency($data->discount_amount);

                return null;
            },
            'contentOptions' => ['class'=>'text-right',
                                 'style'=>'width:10%'],
            'headerOptions'  => ['class'=>'text-right'],
            'visible'        =>  ! $model->isGift() &&
                (\common\models\Payment::PKEY_DROP_SHIPPING != $model->payment_id) &&
                (0 < array_sum(ArrayHelper::getColumn($model->items,'discount_rate'))
                   + array_sum(ArrayHelper::getColumn($model->items,'discount_amount'))),
        ],
        [
            'attribute' => 'quantity',
            'contentOptions' => ['class'=>'text-center',
                                 'style'=>'width: 8%'],
            'headerOptions'  => ['class'=>'text-center'],
        ],
        [
            'attribute' => 'charge',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right',
                                 'style'=>'width:15%'],
            'headerOptions'  => ['class'=>'text-right'],
            'visible'   => ! $model->isGift(),
        ],
    ],
]); ?>

<?php if($model->isGift()): ?>

    <!-- no monetary information -->

<?php elseif(is_array($summaryColumns)): ?>
    <!-- summary columns -->
    <?php if(strtotime($model->create_date) >= \common\models\Tax::newDate()) { ?>
         <p>
          <div style="float:right;width:40%" class="text-right">
    <?php
        $list= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels'  => $taxSummaryColumns,
            'pagination' => false,
        ]),
        'id'         => 'delivery-tax-summary',
        'tableOptions' => ['class'=>'grid-view', 'style' => 'border: solid 0.5px #000000; border-collapse: collapse;'],
        'layout'=>'{items}',
        'columns' => [
            [
                'attribute' => 'tax_rate',
                'label'     => '税率',
                'format'    => 'html',
                'value'     => function($data)use($formatter) { return $data['tax_rate'] < 999 ? $formatter->asPercent($data['tax_rate'] / 100) : '計'; },
                'contentOptions' => ['style'=>'width:20%; border: solid 0.5px #000000; border-collapse: collapse;', 'class'=>'text-center'],
                'headerOptions'  => ['class'=>'text-center', 'style' => 'border: solid 0.5px #000000; border-collapse: collapse;'],

            ],
            [
                'attribute' => 'subtotal',
                'label'     => '税別金額',
                'format'    => 'currency',
                'contentOptions' => ['style'=>'width:40%; border: solid 0.5px #000000; border-collapse: collapse;', 'class'=>'text-right'],
                'headerOptions'  => ['class'=>'text-center', 'style' => 'border: solid 0.5px #000000; border-collapse: collapse;'],
            ],
            [
                'attribute' => 'tax',
                'label'     => '消費税',
                'format'    => 'currency',
                'contentOptions' => ['style'=>'width:40%; border: solid 0.5px #000000; border-collapse: collapse;', 'class'=>'text-right'],
                'headerOptions'  => ['class'=>'text-center', 'style' => 'border: solid 0.5px #000000; border-collapse: collapse;'],
            ],
        ]
    ]); echo $list; ?>
      </div>
    
    <?php } else {
       echo  \yii\widgets\DetailView::widget([
        'id'         => 'delivery-summary',
        'options'    => ['style' => 'border:0; text-align:right'],
        'template'   => '<tr><th class="text-right" style="width:80%">{label}</th><td>{value}</td></tr>',
        'model'      => $model,
        'attributes' => $summaryColumns,
    ]); } ?>

<?= strtotime($model->create_date) >= \common\models\Tax::newDate() ? '
      <p>
      </p>
      <p>「※」は軽減税率対象</p>' : '';
?>
<?php endif ?>

      <p>
      </p>

      <p>&nbsp;</p>
      <p>備考</p>
      <p>
        <?= $model->note ?>
      </p>

    </div>

</div>
