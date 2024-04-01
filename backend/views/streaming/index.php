<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/streaming/index.php $
 * $Id: index.php 2286 2020-04-28 11:28:00Z kawai $
 *
 * @var $this yii\web\View
 * @var $searchModel common\models\StreamingSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

?>
<div class="streaming-index">

    <h1>ライブ配信情報</h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
            'streaming_id',
            [
                'attribute' => 'product_id',
                'format'    => 'raw',
                'value'     =>  function($data){ return $data->product ? $data->product_id.' :   '.Html::a($data->product->name, ['product/view', 'id'=>$data->product_id]) : "";},
            ],
            [
                'attribute' => 'name',
                'format'    => 'raw',
                'value'     => function($data){ return Html::a($data->name, ['view', 'id'=>$data->streaming_id]); },
            ],
            [
                'attribute' => 'expire_from',
                'format'    => 'text',
                'value'     => function($data){ return $data->expire_from; },
            ],
            [
                'attribute' => 'expire_to',
                'format'    => 'text',
                'value'     => function($data){ return $data->expire_to; },
            ],
            [
                'attribute' => 'streaming_url',
                'format'    => 'raw',
                'value'     => function($data){ return Html::a($data->streaming_url);},
            ],
            [
                'attribute' => 'post_url',
                'format'    => 'raw',
                'value'     => function($data){ return Html::a($data->post_url);},
            ],
            [
                'attribute' => 'document_url',
                'format'    => 'raw',
                'value'     => function($data){ return $data->document_url ? Html::a($data->document_url) : 'なし';},
            ],
            [
                'attribute' => 'expire_date',
                'format'    => 'text',
                'value'     => function($data){ return $data->expire_date; },
            ],
            [
                'class' => \yii\grid\ActionColumn::className(),
                'template' => '{view}{update}',
            ],

        ],
    ]); ?>

    <?= Html::a('追加', ['create'], ['class' => 'btn btn-success']) ?>

</div>
