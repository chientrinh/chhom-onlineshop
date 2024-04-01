<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/change-log/index.php $
 * $Id: index.php 2727 2016-07-16 03:31:24Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->params['breadcrumbs'][] = ['label' => '一覧'];
?>
<div class="change-log-index">

    <h1>DB操作履歴</h1>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'create_date',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->create_date,['/change-log/view','id'=>$data->create_date]); },
            ],

            'tbl',
            'pkey',
            [
                'attribute' => 'user_id',
                'headerOptions' => ['class'=>'col-md-1'],
            ],
            'route',
            [
                'attribute' => 'action',
                'filter'    => ['insert'=>'insert','update'=>'update','delete'=>'delete'],
                'headerOptions' => ['class'=>'col-md-1'],
            ],
        ],
    ]); ?>

</div>
