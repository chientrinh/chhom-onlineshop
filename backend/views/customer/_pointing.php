<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/customer/_pointing.php $
 * $Id: _pointing.php 3851 2018-04-24 09:07:27Z mori $
 */

use yii\helpers\Html;
?>
<?= \yii\grid\GridView::widget([
    'dataProvider'   => $dataProvider,
    'layout'         => '{items}{pager}{summary}',
    'tableOptions'   => ['class'=>'table table-condensed table-striped'],
    'summaryOptions' => ['class'=>'small text-right pull-right'],
    'emptyText'      => 'まだありません',
    'showOnEmpty'    => false,
    'columns' => [
        [
            'attribute' => 'pointing_id',
            'format'    => 'html',
            'value'     => function($model){ return Html::a(sprintf('%06d',$model->pointing_id), ['/pointing/view', 'id'=>$model->pointing_id]); },
        ],
        'create_date:date',
        'update_date:date',
        [
            'attribute' => 'company_id',
            'value'     => function($data){ return strtoupper($data->company->key); },
        ],
        [
            'attribute' => 'seller',
            'format'    => 'html',
            'value'     => function($model){ return $model->seller ? Html::a($model->seller->name, ['/customer/view', 'id'=>$model->seller_id]) : null; },
        ],
        [
            'attribute' => 'customer',
            'format'    => 'html',
            'value'     => function($model){ return $model->customer ? Html::a($model->customer->name, ['/customer/view', 'id'=>$model->customer_id]) : null; },
        ],
        [
            'attribute' => 'point_consume',
            'format'    => 'currency',
            'value'     => function($model){ return (0 - $model->point_consume); },
            'contentOptions' => ['class'=>'text-right'],
        ],
        [
            'attribute' => 'point_given',
            'format'    => 'integer',
            'contentOptions' => ['class'=>'text-right'],
        ],
        [
            'attribute' => 'status',
            'value'     => function($model){ return $model->statusName; },
        ],
        [
            'attribute' => 'note',
        ],
        [
            'attribute' => 'staff_id',
            'format'    => 'html',
            'value'     => function($model){ return $model->staff ? $model->staff->name : null; },
        ],
    ],
]); ?>
