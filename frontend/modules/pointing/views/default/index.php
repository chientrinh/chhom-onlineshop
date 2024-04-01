<?php

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/pointing/views/default/index.php $
 * $Id: index.php 2365 2016-04-03 04:18:25Z mori $
 *
 * $searchModel
 * $dataProvider
 */

use \yii\helpers\Html;

$this->params['body_id'] = 'Mypage';

$dataProvider->sort = [
            'attributes' => [
                'create_date',
                'pointing_id',
                'status',
            ],
            'defaultOrder' => ['create_date' => SORT_DESC],
];

?>

<div class="cart-view">

	<h2>
        <span>
        <?= $this->context->module->name ?>
            <small><?= Yii::$app->controller->company->name ?></small>
        </span>
    </h2>

  <div class="col-md-12">

      <!-- <div class="panel panel-default"> -->
          <div class="panel-heading">
              <?= Yii::$app->controller->nav->run() ?>
          </div>
          <!-- </div> -->

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    //'filterModel'  => $searchModel,
    'layout'  => '{items}{pager}{summary}',
    'emptyText'  => '履歴はありません',
    'rowOptions' => function ($model, $key, $index, $grid) { if($model->isExpired()) return ['class'=>'danger']; },
    'columns' => [
        [
            'attribute' => 'pointing_id',
            'format'    => 'html',
            'value'     => function($data)
            {
                return Html::a(sprintf('%06d', $data->pointing_id), ['view','id'=>$data->pointing_id]);
            }
        ],
        [
            'attribute' => 'create_date',
            'format'    => ['date','php:Y-m-d D H:i'],
        ],
        [
            'attribute' => 'customer',
            'value'     => function($data){ return $data->customer ? $data->customer->name : ''; },
        ],
        [
            'attribute' => 'total_charge',
            'format'    => 'currency',
            'contentOptions' => ['class' => 'text-right'],
        ],
        [
            'attribute' => 'point_consume',
            'format'    => 'integer',
            'contentOptions' => ['class' => 'text-right'],
        ],
        [
            'attribute' => 'point_given',
            'format'    => 'integer',
            'contentOptions' => ['class' => 'text-right'],
        ],
        [
            'attribute' => 'status',
            'format'    => 'text',
            'value'     => function($data){ return $data->statusName; },
        ],
        [
            'label'     => '',
            'format'    => 'raw',
            'value'     => function($data){ if(! $data->isExpired()) return Html::a("レシート", ['receipt','id'=>$data->pointing_id]); },
        ],
    ],
])?>

  <?= Html::a('起票する',['create'],['class'=>'btn btn-success']) ?>

  </div>

</div>
