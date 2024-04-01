<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use \common\models\StreamingBuy;
use \common\models\Streaming;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/streaming-buy/view.php $
 * $Id: view.php 2992 2020-04-28 15:37:38Z kawai $
 *
 * @var $this yii\web\View
 * @var $model common\models\StreamingBuy
 */

$this->params['breadcrumbs'][] = ['label' => $model->streaming_buy_id, 'url' => ['view','id'=>$model->streaming_buy_id]];
?>
<div class="streaming-view">

    <p class="pull-right">
        <?= Html::a('編集', ['update', 'id' => $model->streaming_buy_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('',['view','id'=>$model->streaming_buy_id -1],['class'=>'btn btn-sm btn-default glyphicon glyphicon-chevron-left'])?>
        <?= Html::a('',['view','id'=>$model->streaming_buy_id +1],['class'=>'btn btn-sm btn-default glyphicon glyphicon-chevron-right'])?>
    </p>

    <h1><?= 'ライブ配信購入ID '.$model->streaming_buy_id ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'customer_id',
                'format'    => 'raw',
                'value'     => $model->customer_id ? $model->customer_id.' : '.Html::a($model->customer->name, ['customer/view','id'=>$model->customer_id]) : null,
            ],
            [
                'attribute' => 'streaming_id',
                'format'    => 'raw',
                'value'     => Html::a($model->streaming->name, ['streaming/view', 'id'=>$model->streaming_id]),
            ],
            [
                'attribute' => 'create_date',
                'format'    => 'datetime',
                'value'     => $model->create_date,
            ],
            [
                'attribute' => 'update_date',
                'format'    => 'datetime',
                'value'     => $model->update_date,
            ],
            [
                'attribute' => 'expire_date',
                'format'    => 'datetime',
                'value'     => $model->expire_date,
            ],
        ],
    ]); ?>

</div>
