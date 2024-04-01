<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use \common\models\StreamingBuy;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/Streaming/view.php $
 * $Id: view.php 2992 2016-10-19 07:37:38Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Streaming
 */

$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view','id'=>$model->streaming_id]];
?>
<div class="streaming-view">

    <p class="pull-right">
        <?= Html::a('編集', ['update', 'id' => $model->streaming_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('',['view','id'=>$model->streaming_id -1],['class'=>'btn btn-sm btn-default glyphicon glyphicon-chevron-left'])?>
        <?= Html::a('',['view','id'=>$model->streaming_id +1],['class'=>'btn btn-sm btn-default glyphicon glyphicon-chevron-right'])?>
    </p>

    <h1><?= Html::encode($model->name) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'streaming_id',
            [
                'attribute' => 'product_id',
                'format'    => 'raw',
                'value'     => $model->product ? $model->product_id.' :   '.Html::a($model->product->name, ['product/view', 'id'=>$model->product_id]) : "",
            ],
            'name',
            'expire_from',
            'expire_to',
            [
                'attribute' => 'streaming_url',
                'format'    => 'raw',
                'value'     => Html::a($model->streaming_url),
            ],
            [
                'attribute' => 'post_url',
                'format'    => 'raw',
                'value'     => Html::a($model->post_url),
            ],
            [
                'attribute' => 'document_url',
                'format'    => 'raw',
                'value'     => $model->document_url ? Html::a($model->document_url) : 'なし',
            ],            
            'create_date',
            'update_date',
            'expire_date',
        ],
    ]) ?>

    <h2>購入情報</h2>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getStreamingBuy(),
            'sort'  => ['defaultOrder' => ['streaming_buy_id'=>SORT_DESC]],
        ]),
        'columns' => [
            'streaming_buy_id',
            [
                'attribute' => 'customer_id',
                'format'    => 'html',
                'value'     => function($data)
                {
                    return $data->customer_id ? $data->customer_id.' : '.Html::a($data->customer->name, ['customer/view','id'=>$data->customer_id]) : null;
                },
            ],
            'create_date',
            'expire_date',
            'update_date',
        ],
        'rowOptions' => function ($data, $key, $index, $grid)
        {
            if($data->isExpired())
                return ['class'=> 'text-danger'];
        }
    ]) ?>
</div>
