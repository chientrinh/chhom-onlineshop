<?php

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/purchase/refund.php $
 * $Id: refund.php 3758 2017-11-17 03:20:30Z kawai $
 *
 * @var $this yii\web\View
 * @var $model common\models\Purchase
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\ChangeLog;
use common\models\Payment;
use common\models\PurchaseStatus;

$this->params['breadcrumbs'][] = ['label' => '売上', 'url' => 'index'];
$this->params['breadcrumbs'][] = ['label' => $model->purchase_id, 'url'=>['view','id'=>$model->purchase_id]];
$this->params['breadcrumbs'][] = ['label' => '返品'];

$statusList = PurchaseStatus::find()->where(['<=','status_id',PurchaseStatus::PKEY_DONE])->andFilterWhere(['>=','status_id',$model->status])->all();
$statusList = ArrayHelper::map($statusList,'status_id','name');

$query = Payment::find()->where(['payment_id'=>[Payment::PKEY_CASH,
                                                Payment::PKEY_YAMATO_COD,
                                                Payment::PKEY_BANK_TRANSFER,]]);
$payments = ArrayHelper::map($query->all(), 'payment_id','name');

?>

<div class="purchase-view">

    <h1>返品 : <?= Html::encode($model->purchase_id) ?></h1>

    <?php $form = \yii\bootstrap\ActiveForm::begin([
        'fieldConfig' => [
            'template' => "{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
            'horizontalCssClasses' => [
            ],
        ],
    ]) ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels'  => $model->items,
            'pagination' => false,
        ]),
        'layout'  => '{items}',
        'rowOptions' => function($data){ if(isset($data->parent)) return ['class'=>'text-muted']; },
        'columns' => [
            'code',
            [
                'attribute' => 'name',
            ],
            [
                'attribute' => 'price',
                'format'    => 'currency',
                'contentOptions' => ['class'=>'text-right'],
            ],
            [
                'attribute' => 'point_rate',
                'contentOptions' => ['class'=>'text-right'],
                'value'     => function($data){ return intval($data->point_rate) . '%'; },
            ],
            [
                'attribute'=>'discount_rate',
                'value'     => function($data){ return intval($data->discountRate) . '%'; },
                'contentOptions' => ['class'=>'text-right'],
            ],
            [
                'attribute' => 'quantity',
                'format'    => 'integer',
                'contentOptions' => ['class'=>'text-center'],
            ],
            [
                'label'     => '返品数',
                'format'    => 'raw',
                'value'     => function($data)use($input){
                    if(isset($data->parent)) return null;

                    $value = ArrayHelper::getValue($input->quantity, $data->seq, 0);
                    return Html::input('number', "quantity[{$data->seq}]", $value, [
                        'class'=> 'form-control',
                        'min'  => 0,
                        'max'  => $data->quantity,
                    ]);
                },
                'contentOptions' => ['class'=>'col-md-1'],
            ],
            [
                'attribute' => 'charge',
                'format'    => 'currency',
                'contentOptions' => ['class'=>'text-right'],
            ],
            [
                'attribute' => 'pointTotal',
                'format'    => 'integer',
                'contentOptions' => ['class'=>'text-right'],
            ],
        ],
    ]) ?>

    <label class="control-label" for="purchase-note">理由</label>
    <?= $form->field($input, 'note')->textArea(['name'=>'note']) ?>

    <p class="col-md-12 col-sm-12">
    <?= Html::submitButton('確定',['class'=>'btn btn-danger pull-right']) ?>
    </p>

    <?= Html::errorSummary($input, ['class'=>'alert alert-danger']) ?>

    <div class="col-md-6 col-sm-6">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'company_id',
                'format'    => 'text',
                'value'     => $model->company ? strtoupper($model->company->key) : '(指定なし)',
            ],
            [
                'attribute' => 'branch_id',
                'format'    => 'html',
                'value'     => ($b = $model->branch) ? $b->name : null,
            ],
            [
                'attribute' => 'customer_id',
                'format'    => 'html',
                'value'     => ($c = $model->customer) ? $c->name : null,
            ],
            [
                'attribute' => 'payment_id',
                'format'    => 'raw',
                'value'     => $model->payment->name,
            ],
            [
                'attribute' => 'paid',
            ],
        ],
    ]) ?>
    </div>

    <div class="col-md-6 col-sm-6">
    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table table-striped table-bordered detail-view text-right'],
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
                'attribute' => 'postage',
                'format'    => 'currency',
            ],
            [
                'attribute' => 'handling',
                'format'    => 'currency',
            ],
            [
                'attribute' => 'point_consume',
                'format'    => 'currency',
                'value'     => (0 - $model->point_consume),
            ],
            [
                'attribute' => 'discount',
                'format'    => 'currency',
                'value'     => (0 - $model->discount),
            ],
            [
                'attribute' => 'total_charge',
                'format'    => 'raw',
                'value'     => Html::tag('strong', '￥' . number_format($model->total_charge),['class'=>'']),
            ],
            [
                'attribute' => 'receive',
                'format'    => 'currency',
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
    </div>

</div>
