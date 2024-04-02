<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\CustomerFamily;
use common\models\Customer;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/purchase/create.php $
 * @version $Id: create.php 4147 2019-03-29 06:28:58Z kawai $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel  common\models\sodan\Room
 */

$title = '起票';
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => \yii\helpers\Url::current()];
$labels = ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
rsort($labels);
$this->title = implode(' | ', $labels) . ' | '. Yii::$app->name;

$query  = \common\models\Payment::find()->selectPayment();
$payments = ArrayHelper::map($query->all(),'payment_id','name');
if (!$model->client->ysdAccount) {
    unset($payments[common\models\Payment::PKEY_DIRECT_DEBIT]);
} else {
    $model->payment_id = common\models\Payment::PKEY_DIRECT_DEBIT;
}

$query = common\models\Product::find()->sodanCoupon()->active();
$products = ArrayHelper::merge(['' => ''], ArrayHelper::map($query->all(), 'product_id', 'name'));

$jscode = "
$('#discount_rate').click(function(){
    $('<input>', {
        type: 'hidden',
        name: 'change_rate',
        value: '1'
    }).appendTo('form');
    $('form').submit();
 	return true;
});
";
$this->registerJs($jscode);

?>
<div class="col-md-12">

    <?= \common\modules\sodan\widgets\SodanDetail::widget([
        'model' => $model->interview,
        'attributes' => ['itv_date','homoeopath_id','client_id','product_id','status_id'],
    ]) ?>

    <?php $form = \yii\bootstrap\ActiveForm::begin([
        'fieldConfig' => [
        ],
    ]); ?>
    <p>相談会のお会計を起票します。</p>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => $model->purchase->items,
            'pagination' => false,
        ]),
        'layout'   => '{items}',
        'showFooter' => true,
        'columns' => [
            'name',
            [
                'attribute' => 'price',
                'format'    => 'currency',
                'contentOptions' => ['class'=>'text-right'],
            ],
            [
                'attribute' => 'quantity',
                'format'    => 'integer',
                'contentOptions' => ['class'=>'text-right'],
            ],
            [
                'attribute' => 'discount_rate',
                'format'    => 'raw',
                'contentOptions' => ['class'=>'text-right'],
                'value' => function ($data) {
                    $rate_init = ($data->name == 'キャンセル料') ? 40 : 0;
                    return Html::textInput('discount_rate', Yii::$app->request->post('discount_rate', $rate_init), ['class' => 'form-control', 'style' => 'width:80%;'])
                         . Html::button('変更', ['id' => 'discount_rate', 'class' => 'btn btn-xs btn-default']);
                }
            ],
            [
                'attribute' => 'charge',
                'format'    => 'integer',
                'footer'    => '小計　　' . Yii::$app->formatter->asCurrency($model->purchase->subtotal) . '<br>'
                             . '消費税　' . Yii::$app->formatter->asCurrency($model->purchase->tax) . '<br>'
                             . 'お支払い' . Yii::$app->formatter->asCurrency($model->purchase->total_charge),
                'contentOptions' => ['class'=>'text-right'],
                'footerOptions'  => ['class'=>'text-right'],
            ],
        ]
    ]) ?>

    <?= $form->field($model,'payment_id')->dropDownList($payments) ?>
    <?= $form->field($model,'discount')->textInput() ?>

    <?= $form->field($model,'commission')->textInput() ?>

    <?= $form->field($model,'note')->textArea() ?>

    <?= Html::hiddenInput('itv_id', $model->interview->itv_id) ?>

    <?= Html::submitButton('確定',['class'=>'btn btn-danger']) ?>

    <?php $form->end() ?>

    <?php if($model->hasErrors()): ?>
        <?= Html::errorSummary($model,['class'=>'alert alert-danger']) ?>
    <?php endif ?>

</div>
