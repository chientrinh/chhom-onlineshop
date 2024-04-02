<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/coupon/index.php $
 * $Id: index.php 1876 2015-12-15 15:53:35Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

?>
<div class="wait-list-index">

    <p class="pull-right">
        <?= Html::a('追加', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <h1>健康相談クーポン</h1>
    <p>健康相談会計時に使用できるクーポンを管理します。</p>
    <?= \yii\grid\GridView::widget([
    'tableOptions' => ['class'=>'table table-condensed table-striped'],
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'showOnEmpty'  => true,
        'layout' => "{items}\n{pager}",
        'columns' => [
            [
                'attribute' => 'product_id',
                'format'    => 'html',
                'value'     => function($data){
                    return Html::a(sprintf('%06d', $data->product_id), ['view','id' => $data->product_id]);
                },
                'filter' => false
            ],
            'name',
            'kana',
            [
                'attribute' => 'price',
                'format'    => 'html',
                'value'     => function ($data) { return sprintf("&yen;%s", number_format($data->price)); },
                'filter'    => false
            ],
            [
                'attribute' => 'start_date',
                'format'    => 'html',
                'value'     => function($data){ return $data->start_date; },
                'filter'    => false
            ],
            [
                'attribute' => 'expire_date',
                'format'    => 'html',
                'value'     => function($data){ return $data->expire_date; },
                'filter'    => false
            ],
        ],
    ]); ?>
</div>
