<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/views/history/view.php $
 * $Id: view.php 4248 2020-04-24 16:29:45Z mori $
 *
 * $model
 */

use \yii\helpers\Html;

$this->params['breadcrumbs'][] = ['label'=>sprintf('注文番号: %06d', $model->purchase_id)];

$widget = new \frontend\widgets\CartItemColumn([
    'cart_idx'=> null,
    'purchase'=> $model,
    'items'   => $model->items,
]);

?>

<div class="profile-history-index">

    <h1 class="mainTitle">マイページ</h1>
	<p class="mainLead">お客様ご本人のご購入履歴やお届け先の閲覧・編集などができます。</p>

    <div class="col-md-3">
    <div class="Mypage-Nav">
	<div class="inner">
    <h3>Menu</h3>
    <?= Yii::$app->controller->nav->run() ?>
	</div>
    </div>
    </div>
    
    <div class="col-md-9">
    <h2><span>注文番号: <?= sprintf('%06d',$model->purchase_id) ?> </span></h2>

<?= \yii\widgets\DetailView::widget([
    'model'  => $model,
    'attributes' => [
        'create_date',
        [
            'attribute' => 'payment_id',
            'value'     => $model->payment->name,
        ],
        [
            'attribute' => 'status',
            'value'     => $model->statusName,
        ],
        [
            'label'     => '納品書金額表示',
            'value'     => ($deliv = $model->delivery) ? $deliv->giftName : '表示',
            'visible'   => $model->checkForGift(),
        ],
    ],
]) ?>

    <h3><span class="mypage-history">ご注文明細</span></h3>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => $model->items,
            'pagination' => false,
        ]),
        'tableOptions' => ['summary'=>"ご注文明細", 'class'=>'table table-striped table-bordered'],
        'summary' => '全 {totalCount} 件',
        'layout' => '{items}{pager}',
        'columns'      => [
            [
                'label'    => "商品画像",
                'format'   => 'html',
                'value'    => function($data,$key,$idx,$col) use ($widget)
                {
                    return $widget->renderImageColumn($idx);
                },
            ],
            [
                'attribute' => 'name',
                'format'    => 'html',
                'headerOptions' => ['class'=>'Date'],
                'value' => function($data,$key,$idx,$col) use ($widget)
                {
                    return $widget->renderLabelColumn($idx, true);
                },
            ],
            [
                'attribute'=> 'price',
                'label'    => '価格',
                'format'   => 'html',
                'contentOptions' => ['class'=>'text-right'],
                'headerOptions' => ['class'=>'Price'],
                'value'         => function($data,$key,$idx,$col) use($widget)
                {
                    return $widget->renderPriceColumn($idx);
                },
            ],
            [
                'attribute'=> 'qty',
                'label'    => '数量',
                'format'   => 'raw',
                'value'         => function($model,$key,$idx,$col)use($widget)
                {
                    return $widget->renderQtyColumn($idx);
                },
                'contentOptions' => ['class'=>'text-right col-md-2'],
                'headerOptions'  => ['class'=>'qty'],
                'footer'         => '小計<br>消費税',
            ],
            [
                'attribute'      => 'charge',
                'label'          => '',
                'format'         => 'html',
                'contentOptions' => ['class'=>'text-right'],
                'headerOptions'  => ['class'=>'sum'],
                'value'          => function($model,$key,$idx,$col) use($widget)
                {
                    return $widget->renderChargeColumn($idx);
                },
            ],
        ],
]); ?>

<?= \yii\widgets\DetailView::widget([
    'model'  => $model,
    'template' => '<tr><th>{label}</th><td class="text-right">{value}</td></tr>',
    'attributes' => [
        [
            'attribute'=>'subtotal',
            'format'   => 'currency',
        ],
        [
            'attribute'=>'tax',
            'format'   => 'currency',
        ],
        [
            'attribute'=>'postage',
            'format'   => 'currency',
        ],
        [
            'attribute'=>'handling',
            'format'   => 'currency',
        ],
        [
            'attribute'=>'point_consume',
            'format'   => 'currency',
            'value'    => 0 - (int)$model->point_consume,
        ],
        [
            'attribute' => 'discount',
            'format'    => 'currency',
            'value'     => (0 - $model->discount),
        ],
        [
            'attribute'=>'total_charge',
            'format'   => 'currency',
        ],
        [
            'attribute'=>'point_given',
            'value'    => 'pt '. number_format((int)$model->point_given),
        ],
        [
            'attribute'=> 'commissions.fee',
            'format'   => 'currency',
            'value'    => array_sum(\yii\helpers\ArrayHelper::getColumn($model->commissions,'fee')),
            'visible'  => $model->commissions,
        ],
    ],
]) ?>

	<h3><span class="mypage-history">お届け先</span></h3>
    <?php if(! $model->delivery): ?>
	<p class="windowtext">お届け先はありません。</p>
    <?php else: ?>

<?= \yii\widgets\DetailView::widget([
    'model'  => $model->delivery,
    'attributes' => [
        [
            'attribute' => 'addr',
            'format' => 'html',
            'value'     => "〒".$model->delivery->zip.'&nbsp;'.$model->delivery->addr,
        ],
        'name',
        'tel',
        [
            'attribute' => 'expect_date',
            'value'     => $model->delivery->datetimeString,
        ],
    ],
]) ?>

<?php endif ?>

   <h3><span>メール履歴</span></h3>

   <table class="table table-striped table-bordered detail-view">
<?= \yii\grid\GridView::begin([
   'dataProvider' => new \yii\data\ArrayDataProvider([
       'allModels'    => $model->mails,
       'pagination' => false,
   ]),
   'layout' => '{items}',
   'columns' => [
       'date:date',
       [
           'attribute' => 'subject',
           'format'    => 'html',
           'value'     => function($data)use($model){ return Html::a($data->subject, ['view','id'=>$model->purchase_id,'mail_id'=>$data->mailer_id]); },
       ],
   ],
   ])->renderTableBody() ?>
   </table>

    </div><!-- col-md-9 -->

</div>
