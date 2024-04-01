<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/pointing/views/default/_form.php $
 * $Id: _form.php 3620 2017-09-29 08:04:43Z kawai $
 *
 * @var $this yii\web\View
 * @var $model common\models\PointingForm
 */

$this->params['body_id'] = 'Mypage';

$csscode ="
strong {
padding-left: 5px;
padding-right:5px;
}
";
$this->registerCss($csscode);

$jscode = "
$( document ).ready(function() {
    document.getElementById('barcode').focus();
});

$('input').change(function(){
    $(this).submit();
});
$('textarea').change(function(){
    $(this).submit();
});
";
$this->registerJs($jscode);

$searchModel = new \common\components\ean13\ModelFinder();
?>

<div class="cart-view">

	<h2>
        <span>
        <?= $this->context->module->name ?>
            <small><?= Yii::$app->controller->company->name ?></small>
        </span>
    </h2>

  <div class="col-md-12">

      <div class="panel-heading">
          <?= Yii::$app->controller->nav->run() ?>
      </div>

      <div class="panel-heading">
          <?= Yii::$app->controller->nav2->run() ?>
      </div>


    <?php $form = ActiveForm::begin([
        'id'     => 'barcode-form',
        'action' => ['apply','target'=>'barcode'],
        'method' => 'get',
        'fieldConfig' => [
            'template' => "{input}\n{hint}\n{error}",
        ],
    ]) ?>
    <?= $form->field($searchModel, 'barcode')->textInput(['id'=>'barcode','name'=>'barcode','placeholder'=>'バーコード、商品コード、会員証NOなど']) ?>
    <?php $form->end() ?>

<?php
$dataProvider = new \yii\data\ArrayDataProvider([
    'allModels' => $model->items,
    'pagination'=> false,
]);

if($model->pointing_id)
    $dataProvider->sort = [
        'attributes' => [
            'remedy' => [
                'default' => SORT_DESC,
            ],
            'remedy' => [
                'asc' => ['remedy.abbr' => SORT_ASC  ],
                'desc'=> ['remedy.abbr' => SORT_DESC ],
                'default' => SORT_ASC,
            ],
            'potency' => [
                'asc' => ['potency.weight' => SORT_ASC  ],
                'desc'=> ['potency.weight' => SORT_DESC ],
                'default' => SORT_ASC,
            ],
            'vial' => [
                'asc' => ['vial_id' => SORT_ASC  ],
                'desc'=> ['vial_id' => SORT_DESC ],
                'default' => SORT_ASC,
            ],
            'seq',
            'quantity',
        ],
        'defaultOrder' => ['seq' => SORT_ASC ],
    ];
?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'caption' => sprintf('商品 (合計 %d 点)', $model->itemCount),
    'layout'  => '{items}',
    'columns' => [
        [
            'attribute' => 'code',
            'label'     => 'バーコード'
        ],
        [
            'attribute' => 'name',
            'label'     => '品名',
            'format' => 'html',
            'value' => function($data, $key, $index, $column)
            {
                if(isset($data->remedy_id) && $data->remedy)
                {
                    $label = $data->name;
                }
                else
                {
                    $label = nl2br($data->name);
                }
                return Html::a('　×　', ['apply','target'=>'quantity','seq'=>$index,'vol'=>(0 - $data->quantity)],['class'=>'pull-right','title'=>'削除します'])
                     . $label;
            }
        ],
        [
            'attribute' => 'price',
            'label'     => '価格',
            'format'    => 'currency',
            'contentOptions' => ['class' => 'text-right'],
        ],
        [
            'attribute' => 'point_rate',
            'label'     => 'ポイント率',
            'value'     => function($data){ return sprintf('%d %%', intval($data->point_rate)); },
            'contentOptions' => ['class' => 'text-center'],
        ],
        [
            'attribute'=>'quantity',
            'label'     => '数量',
            'format'   =>'html',
            'value'    => function($data,$key,$index,$column)
            {
                return Html::a('-',['apply','target'=>'quantity','seq'=>$index,'vol'=>'-1'],['class'=>'badge'])
                    .  Html::tag('strong',' '.$data->quantity.' ',[
                        'class'=> 'alert-text',
                    ])
                    .  Html::a('+',['apply','target'=>'quantity','seq'=>$index,'vol'=>'1'],['class'=>'badge']);
            }
        ],
        [
            'attribute' => 'basePrice',
            'label'     => '定価小計',
            'format'    => 'currency',
            'contentOptions' => ['class' => 'text-right'],
        ],
    ],
])?>

<div class="form-group">
    <?= Html::a('追加', ['search','target'=>'product'],['class'=>'btn btn-success'])?>
    <?= Html::a('すべて削除', ['apply','target'=>'reset'],['class'=>'btn btn-default'])?>
    <?= Html::a('確定', ['finish'],[
        'class'=>'btn pull-right ' . ($model->hasErrors() ? ' btn-default' : ' btn-danger'),
        'title'=>'起票を確定します。このボタンを押すまで、作成中の内容は約2時間サーバに保存されます。',
    ])?>

      <?php if($model->hasErrors()): ?>
       <?= Html::errorSummary($model,['class'=>'alert alert-warning']) ?>
      <?php endif ?>
</div>

<div class="col-md-6 col-xs-12 pull-right">

<?php $form = ActiveForm::begin([
    'id'     => 'summary-form',
    'action' => ['apply','target'=>'summary'],
    'method' => 'get',
    'fieldConfig' => [
        'template' => "{input}\n{hint}\n{error}",
    ],
    'enableClientValidation' => false,
]) ?>

    <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table table-striped table-bordered detail-view text-right col-md-6'],
        'attributes' => [
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
                'format'    => 'raw',
                'value'     => $form->field($model,'point_consume')->textInput(['name'=>'point_consume','class'=>'form-control text-right js-zenkaku-to-hankaku']),
                'visible'   => (0 < $model->customer_id),
            ],
            [
                'attribute' => 'total_charge',
                'format'    => 'raw',
                'value'     => Html::tag('strong', '￥' . number_format($model->total_charge),[]),
            ],
            [
                'attribute' => 'receive',
                'format'    => 'raw',
                'value'     => $form->field($model,'receive')->textInput(['name'=>'receive','class'=>'form-control text-right js-zenkaku-to-hankaku']),
            ],
            [
                'attribute' => 'change',
                'format'    => 'currency',
            ],
            [
                'attribute' => 'point_given',
                'format'    => 'integer',
            ],
        ],
    ]) ?>

    <div class="well">
        <?= $model->getAttributeLabel('note') ?>
        <?= $form->field($model,'note')->textArea(['name'=>'note']) ?>
    </div>

<?php $form->end() ?>
</div>

  <div class="col-md-6 col-xs-12 pull-left well">
      <p>
        お客様
        <?php if($model->customer): ?>
       <?= Html::a('変更', ['search','target'=>'customer'],['class'=>'btn btn-default'])?>
        <?php else: ?>
       <?= Html::a('検索', ['search','target'=>'customer'],['class'=>'btn btn-default'])?>
        <?php endif ?>
      </p>

      <?php if($model->customer): ?>
      <p class="pull-right">
          会員証NO <kbd><?= $model->customer->code ?></kbd>
      </p>
      <p class="col-xs-offset-1">
        <?= $model->customer->name ?> (<?= $model->customer->kana ?>)
        <br>
        <?= $model->customer->grade->name ?> 会員
        <br>
        豊受モール ポイント <strong><?= number_format($model->customer->point) ?> pt</strong>
      </p>
      <?php else: ?>
      (指定がありません)
      <?php endif ?>

  </div><!-- well -->

<?php if($model->isNewRecord): ?>
<?php else: ?>
<?= \yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'attribute' => 'pointing_id',
            'value'     => sprintf('%06d', $model->pointing_id),
        ],
        [
            'attribute' => 'create_date',
            'format'    => ['date', 'php:Y-m-d D H:i'],
        ],
        [
            'attribute' => 'update_date',
            'format'    => ['date', 'php:Y-m-d D H:i'],
        ],
    ],
]) ?>
<?php endif ?>

  </div>

</div>
