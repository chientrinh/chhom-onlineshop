<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/streaming-buy/index.php $
 * $Id: index.php 2286 2020-04-28 11:28:00Z kawai $
 *
 * @var $this yii\web\View
 * @var $searchModel common\models\StreamingSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

?>
<div class="streaming-index">

    <h1>ライブ配信購入情報</h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [            
            'streaming_buy_id',
            [
                'attribute' => 'customer_id',
                'format'    => 'raw',
                'value'     => function($data){ return $data->customer_id ? $data->customer_id.' : '.Html::a($data->customer->name, ['customer/view','id'=>$data->customer_id]) : null; },
            ],
            [
                'attribute' => 'streaming_id',
                'format'    => 'raw',
                'value'     => function($data){ return Html::a($data->streaming->name, ['streaming/view', 'id'=>$data->streaming_id]); },
            ],
            [
                'attribute' => 'create_date',
                'format'    => 'datetime',
                'value'     => function($data){ return $data->create_date; },
            ],
            [
                'attribute' => 'update_date',
                'format'    => 'datetime',
                'value'     => function($data){ return $data->update_date; },
            ],
            [
                'attribute' => 'expire_date',
                'format'    => 'datetime',
                'value'     => function($data){ return $data->expire_date; },
            ],
            [
                'class' => \yii\grid\ActionColumn::className(),
                'template' => '{view}{update}<br>{delete}',
            ],
        ],
    ]); ?>

    <?= Html::a('追加', ['create'], ['class' => 'btn btn-success']) ?>

</div>
