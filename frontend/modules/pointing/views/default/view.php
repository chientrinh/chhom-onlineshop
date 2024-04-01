<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Pointing */

$this->params['body_id'] = 'Mypage';

?>
<div class="cart-view">

	<h2>
        <span>
        <?= $this->context->module->name ?>
            <small><?= Yii::$app->controller->company->name ?></small>
        </span>
    </h2>

    <h3>
        <small>
            <?= $model->getAttributeLabel('pointing_id') ?> : 
        </small>
        <?= sprintf('%06d', $model->pointing_id) ?>
    </h3>

    <p>
        <?= Html::a('レシート', ['receipt', 'id' => $model->pointing_id], ['class' => 'btn btn-default']) ?>
    </p>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query'      => $model->getItems(),
            'pagination' => false,
            'sort'       => false,
        ]),
        'summary' => sprintf("合計 %d 点", $model->itemCount),
        'columns' => [
            'code',
            'name',
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
                'attribute' => 'quantity',
                'format'    => 'integer',
                'contentOptions' => ['class'=>'text-center'],
            ],
            [
                'attribute' => 'basePrice',
                'format'    => 'currency',
                'contentOptions' => ['class'=>'text-right'],
            ],
        ]
    ])?>
    <div class="well">
        <strong><?= $model->getAttributeLabel('note') ?></strong>
        <?= Html::encode(strlen($model->note) ? $model->note : "(なし)") ?>
    </div>

    <div class="row">

    <div class="col-md-6" style="float:right">
    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table table-striped table-bordered detail-view text-right'],
        'attributes' => [
            [
                'attribute' => 'subtotal',
                'format'    => 'currency',
            ],
            [
                'attribute' => 'point_consume',
                'format'    => 'currency',
                'value'     => (0 - $model->point_consume),
            ],
            [
                'attribute' => 'tax',
                'format'    => 'currency',
            ],
            [
                'attribute' => 'total_charge',
                'format'    => 'raw',
                'value'     => Html::tag('strong', '￥' . number_format($model->total_charge),[]),
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

    <div class="col-md-6">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'seller',
                'value'     => $model->seller->name,
            ],
            [
                'attribute' => 'customer',
                'value'     => $model->customer ? $model->customer->name : null,
            ],
            'create_date',
            'update_date',
            [
                'attribute' => 'status',
                'value'     => $model->statusName,
            ],
        ],
    ]) ?>
    </div>

    </div>

<?php if(! $model->isExpired()): ?>
    <p>
        <?= Html::a('修正', ['update', 'id' => $model->pointing_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('無効にする', ['expire', 'id' => $model->pointing_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'ほんとうにこの伝票を無効にしますか?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
<?php endif ?>

</div>
