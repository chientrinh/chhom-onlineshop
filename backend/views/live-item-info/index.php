<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/live-item-info/index.php $
 * $Id: index.php 2286 2020-04-28 11:28:00Z kawai $
 *
 * @var $this yii\web\View
 * @var $searchModel common\models\LiveInfoSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

?>
<div class="live-item-info-index">

    <h1>ライブ配信追加情報_商品リンク</h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'format'    => 'raw',
            ],
            [
                'label' => 'チケット追加情報',
                'attribute' => 'info_id',
                'format'    => 'html',
                'value'     =>  function($data){ return $data->info ? $data->info_id.' : '.Html::a($data->info->name, ['live-info/view', 'id'=>$data->info_id]) : "";},
            ],
            [
                'attribute' => 'product_id',
                'format'    => 'raw',
                'value'     => function($data){ return $data->product_id ? $data->product_id.' : '.Html::a($data->product->name, ['product/view', 'id'=>$data->product_id]) : ""; },
            ],
            [
                'label' => '詳細表示・編集',
                'format'    => 'raw',
                'value'     => function($data){ return Html::a('詳細', ['view', 'id' => $data->id], ['class' => 'btn btn-default']).'　'.Html::a('編集', ['update', 'id' => $data->id], ['class' => 'btn btn-primary']);},
            ],
        ],
    ]); ?>

    <?= Html::a('追加', ['create'], ['class' => 'btn btn-success']) ?>

</div>
