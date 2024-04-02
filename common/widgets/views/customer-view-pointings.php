<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/views/customer-view-pointings.php $
 * $Id: customer-view-pointings.php 1818 2015-11-16 18:26:27Z mori $
 *
 * @var $this  yii/web/View
 * @var $model common/models/Customer
 * @var $query ActiveQuery for Pointing
 */

use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;

?>

<div class="col-md-12">
    <h3>
        <small>
            ポイント付与の履歴
        </small>
    </h3>

<?= \yii\grid\GridView::widget([
    'dataProvider'=> new \yii\data\ActiveDataProvider([
        'query' => $query,
        'pagination'=> [
            'pageSize'  => 10,
            'pageParam' => 'p1-page'
        ],
        'sort' => [
            'sortParam' => 'p1-sort',
            'defaultOrder' => ['pointing_id' => SORT_DESC],
        ]
    ]),
    'layout'         => '{items}{pager}{summary}',
    'tableOptions'   => ['class'=>'table table-condensed table-striped'],
    'summaryOptions' => ['class'=>'small text-right pull-right'],
    'emptyText'      => 'まだありません',
    'showOnEmpty'    => false,
    'columns' => [
        [
            'attribute' => 'pointing_id',
            'format'    => 'html',
            'value'     => function($model){ return Html::a(sprintf('%06d',$model->pointing_id), [sprintf('/pointing/%s/view',$model->company->key), 'id'=>$model->pointing_id]); },
            'visible'   => ! $backend,
        ],
        [
            'attribute' => 'pointing_id',
            'format'    => 'html',
            'value'     => function($model){ return Html::a(sprintf('%06d',$model->pointing_id), ['/pointing/view', 'id'=>$model->pointing_id]); },
            'visible'   => $backend,
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
    ],
]); ?>

</div>

