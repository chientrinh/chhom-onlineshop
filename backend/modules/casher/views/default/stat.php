<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/stat.php $
 * $Id: stat.php 3974 2018-07-31 06:52:50Z mori $
 */

use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\helpers\ArrayHelper;
use miloschuman\highcharts\Highcharts;
use common\models\Payment;

$this->params['breadcrumbs'][] = ['label' => '集計', 'url' => ['stat']];

$jscode = "
$('input').change(function(){
    $(this).submit();
    return false;
});
";

$this->registerJs($jscode);
?>

<div class="dispatch-default-index">
  <div class="body-content">

    <div class="list-group col-md-2">
        <?= $this->render('_menu') ?>
    </div>

    <div class="col-md-10">

    <h2><?= $branch->name ?></h2>

<?php $form = \yii\bootstrap\ActiveForm::begin([
    'method' => 'get',
    'action' => Url::current(['start_date' => null, 'end_date' => null]),
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label'   => 'col-sm-4',
            'wrapper' => 'col-sm-8',
            'error'   => '',
            'hint'    => '',
        ],        
    ],
]) ?>

<?= $form->field($model, 'start_date')->TextInput([
    'name' => 'start_date',
    'filter' => \yii\jui\DatePicker::widget([
        'model'      => $model,
        'attribute'  => 'start_date',
        'language'   => 'ja',
        'dateFormat' => 'yyyy-MM-dd 00:00:00',
        'options'    => ['class'=>'form-control col-md-6'],
    ])
])?>

<?= $form->field($model, 'end_date')->TextInput([
    'name' => 'end_date',
    'filter' => \yii\jui\DatePicker::widget([
        'model'      => $model,
        'attribute'  => 'end_date',
        'language'   => 'ja',
        'dateFormat' => 'yyyy-MM-dd 23:59:59',
        'options'    => ['class'=>'form-control col-md-6'],
    ])
])?>

<div class="col-md-4">
<?php
$matrix = [];
$keys = $model->query->select('payment_id')->distinct()->column();
foreach($keys as $payment_id)
{
    $q       = clone($model->query);
    $payment = Payment::findOne($payment_id);
    $matrix[] = [
        'name' => $payment->name,
        'y'    => (int) $q->andWhere(['payment_id' => $payment_id])->sum('total_charge'),
    ];
}
?>
<?= Highcharts::widget([
   'options' => [
      'title'  => ['text' => $model->getAttributeLabel('payment_id')],
      'series' => [[
          'type' => 'pie',
          'name' => "総計",
          'data' => $matrix,
          'showInLegend' => false,
          'dataLabels'   => [ 'enabled' => true ],
      ]]
   ]
]) ?>
</div>

<div class="col-md-8">
<div id="tab" class="btn-group pull-right" data-toggle="buttons">
<label class="btn btn-success btn-xl <?= Yii::$app->request->get('class') ? 'active' : null ?>">
    <input type="radio" name="class" id="class0" value="0">
    すべて
</label>
<label class="btn btn-success btn-xl <?= Yii::$app->request->get('class') ? 'active' : null ?>">
    <input type="radio" name="class" id="class1" value="1">
    酒販
</label>
<label class="btn btn-success btn-xl <?= Yii::$app->request->get('class') ? 'active' : null ?>">
    <input type="radio" name="class" id="class2" value="2">
    レストラン
</label>
</div>
<div id="tab" class="btn-group pull-right" data-toggle="buttons">
<?php
$radioList = array_merge([0=>'すべて'],ArrayHelper::map(\common\models\Company::find()->each(),'company_id','key'));
foreach($radioList as $pkey => $name): ?>
  <label class="btn btn-primary btn-xl <?= $pkey == Yii::$app->request->get('company') ? 'active' : null ?>">
    <input type="radio" name="company" id="<?= sprintf('company%d',$pkey) ?>" value="<?= $pkey ?>">
    <?= strtoupper($name) ?>
  </label>
<?php endforeach ?>
</div>

<?= \yii\widgets\DetailView::widget([
    'model' => $model,
    'options' => ['class' => 'table table-condensed detail-view text-right'],
    'attributes' => [
        [
            'attribute' => 'purchaseCount',
            'format'    => 'integer',
        ],
        [
            'attribute' => 'itemCount',
            'format'    => 'integer',
        ],
        [
            'attribute' => 'subtotal',
            'format'    => 'integer',
        ],
        [
            'attribute' => 'tax',
            'format'    => 'decimal',
        ],
        [
            'attribute' => 'pointConsume',
            'format'    => 'decimal',
            'value'     => 0 - abs($model->pointConsume),
        ],
        [
            'attribute' => 'discount',
            'format'    => 'decimal',
            'value'     => 0 - abs($model->discount),
        ],
        [
            'attribute' => 'postage',
            'format'    => 'decimal',
        ],
        [
            'attribute' => 'handling',
            'format'    => 'decimal',
        ],
        [
            'attribute' => 'totalCharge',
            'format'    => 'decimal',
        ],
        [
            'attribute' => 'share',
            'format'    => ['percent', 4],
        ],
    ],
]) ?>
    
        <?= Html::a('集計CSV出力',
                    ['print-stat', 'start_date' => $model->start_date, 'end_date' => $model->end_date, 'company' => $model->company_id, 'branch' => $model->branch_id],
                    ['class' => 'btn btn-default pull-right',
                     'style' => 'margin-left:1em;',   
                     'title' => '現在表示している売上集計データをCSVに出力します',
                    ]) ?>
      
        <?= Html::a('売上データCSV出力',
                    ['header-print-stat', 'start_date' => $model->start_date, 'end_date' => $model->end_date, 'company' => $model->company_id, 'branch' => $model->branch_id],
                    ['class' => 'btn btn-default pull-right',
                     'title' => '期間内の売上データをCSVに出力します',
                    ]) ?>
</div>
    </div>

<?php $form->end() ?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $model->itemProvider,
    'layout'  => '{items}{pager}{summary}',
    'columns' => [
        ['class'=> \yii\grid\SerialColumn::className() ],
        [
            'attribute' => 'category',
            'label'     => 'カテゴリー',
            'format'    => 'html',
            'value'     => function($data)
            {
                $model = \common\models\Product::findOne(ArrayHelper::getValue($data, 'product_id'));
                if($model)
                    return $model->category->name;

                $model = \common\models\RemedyStock::findOne([
                    'remedy_id' => ArrayHelper::getValue($data, 'remedy_id'),
                ]);
                if($model)
                    return $model->category->name;

                return null;
            },
        ],
        [
            'attribute' => 'code',
            'label'     => 'コード',
            'format'    => 'html',
            'value'     => function($data)
            {
                $code = ArrayHelper::getValue($data, 'code');
                // 野菜かどうかは、Vegetable：EAN13＿PREFIXに先頭が一致するかで判定できる
                $veg_prefix = \common\models\Vegetable::EAN13_PREFIX;

                if($veg_prefix == substr($code, 0, strlen($veg_prefix))) {
                    $veg_id = substr($code, 2, 5);
                    return $veg_id; 
                }

                return $code;
            },
        ],
        [
            'attribute' => 'name',
            'label'     => '商品名',
            'format'    => 'html',
        ],
        [
            'attribute' => 'price',
            'label'     => '定価',
            'format'    => 'currency',
            'value'     => function($data)
            {
                // 生野菜という商品と紐付く、野菜管理上の商品でない限り、価格を表示させる
                $model = \common\models\Product::findOne(ArrayHelper::getValue($data, 'product_id'));
                if($model && $model->name == \common\models\Vegetable::PRODUCT_NAME) {
                    $price = null;
                } else {
                    $price = ArrayHelper::getValue($data, 'price');
                }
                return $price;
            },
            'contentOptions' => ['class'=>'text-right'],
        ],
        [
            'attribute' => 'unit_tax',
            'label'     => '消費税',
            'format'    => 'currency',
            'value'     => function($data)
            {
                // 生野菜という商品と紐付く、野菜管理上の商品でない限り、消費税を表示させる
                $model = \common\models\Product::findOne(ArrayHelper::getValue($data, 'product_id'));
                if($model && $model->name == \common\models\Vegetable::PRODUCT_NAME) {
                    $tax = null;
                } else {
                    $tax = ArrayHelper::getValue($data, 'unit_tax');
                }
                return $tax;
            },
            'contentOptions' => ['class'=>'text-right'],
        ],
        [
            'attribute' => 'quantity',
            'label'     => '数量',
            'format'    => 'integer',
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => array_sum(ArrayHelper::getColumn($model->itemProvider->models, 'quantity')),
        ],
        [
            'attribute' => 'basePrice',
            'label'     => '商品計',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => '￥'.number_format(array_sum(ArrayHelper::getColumn($model->itemProvider->models, 'basePrice'))),
        ],
        [
            'attribute' => 'discountTotal',
            'label'     => '値引計',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => '￥'.number_format(array_sum(ArrayHelper::getColumn($model->itemProvider->models, 'discountTotal'))),
        ],
        [
            'attribute' => 'pointTotal',
            'label'     => 'ポイント計',
            'format'    => 'integer',
            'contentOptions' => ['class'=>'text-right'],
            'footer'    => number_format(array_sum(ArrayHelper::getColumn($model->itemProvider->models, 'pointTotal'))),
        ],
    ],
    'showFooter' => true,
    'footerRowOptions' => ['class'=>'text-right','style'=>'font-weight:bold'],
]) ?>

  </div>

</div>

