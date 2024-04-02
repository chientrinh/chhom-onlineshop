<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/cart/views/default/thankyou.php $
 * $Id: thankyou.php 2625 2016-06-26 01:39:06Z mori $
 *
 * $cart_idx integer
 * $model    Cart model
 */

use yii\helpers\Html;
use yii\helpers\Url;

$title = $purchase->branch_id == \common\models\Branch::PKEY_HOMOEOPATHY_TOKYO ? '相談会料金の決済が完了しました': "ご購入ありがとうございます";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Cart';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));


$widget = new \frontend\widgets\CartItemColumn([
  'cart_idx'=> null,
  'purchase'=> $purchase,
  'items'   => $purchase->items,
]);
$formatter = new \yii\i18n\Formatter();
?>

<div class="cart-default-thankyou">
  <h1 class="mainTitle"><?php echo $title;?></h1>

<div class="col-md-12">

</div><!-- col-md-12 -->


<div class="col-md-12">

<?= \yii\widgets\DetailView::widget([
    'model'  => $purchase,
    'attributes' => [
        [
            'attribute' => 'purchase_id',
            // 'value'     => $purchase->payment->name,
        ],
        'create_date',
        [
            'attribute' => 'payment_id',
            'value'     => $purchase->payment->name,
        ],
    ],
]) ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => $purchase->items,
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
                    return $widget->renderLabelColumn($idx);
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
        ],
]); ?>

<?php $table_title = $purchase->branch_id == \common\models\Branch::PKEY_HOMOEOPATHY_TOKYO ? '決済の履歴': "ライブ配信情報";?>

</div><!-- col-md-8 -->
<div class="col-md-8">
<div class="Detail-Total">
    <div class="inner">
        <h4><?php echo $table_title ?></h4>

<?php if($purchase->customer && $purchase->branch_id == \common\models\Branch::PKEY_HOMOEOPATHY_TOKYO) { ?>
<p>決済の履歴は、豊受モールのマイページ⇒ご購入の履歴からご確認ください</p>
<p>
 <?= Html::a('https://mall.toyouke.com/index.php/profile/history/index','https://mall.toyouke.com/index.php/profile/history/index') ?>
 </p>
<?php } else if($purchase->customer) { ?>
    <p>※豊受会員のみなさまへ<br/>
　ライブ配信は、マイページから視聴が可能です。ログイン後、マイページをご確認ください。
</p>
<p>
 <?= Html::a('https://ec.homoeopathy.ac/profile/default/index','https://ec.homoeopathy.ac/profile/default/index') ?>
 </p>
<?php } else { ?>
<p>※ライブ視聴URLは、各日程とも共通になります。<br/>
<p>【URL】
 <?= Html::a('https://stream.homoeopathy.ac/live/'.$purchase->purchase_id) ?>
 </p>
 </p>
<?php } ?>
<?php if($purchase->branch_id !==  \common\models\Branch::PKEY_HOMOEOPATHY_TOKYO) { ?>
<p>
▼ライブ配信に関するお問い合わせ<br />
E-mail: ec-chhom@homoeopathy.ac
</p>
<?php } ?>

</div>
</div>
</div>

<hr>



<div class="col-md-4">
  <div class="Detail-Total">
    <div class="inner">

<?= \yii\widgets\DetailView::widget([
    'model' => $purchase,
        'template' => '<tr><th>{label}</th><td class="text-right">{value}</td></tr>',
    'attributes' => [
        'subtotal:currency',
        'tax:currency',
        [
            'attribute'=> 'total_charge',
            'format'   => 'raw',
            'value'    => Html::tag('span', $formatter->asCurrency($purchase->total_charge),['class'=>'Total']),
        ],
    ],
]);?>
</div><!-- col-md-4 -->
</div>
</div>
