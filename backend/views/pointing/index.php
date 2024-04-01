<?php

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/pointing/index.php $
 * $Id: index.php 3832 2018-02-02 09:00:16Z kawai $
 *
 * $searchModel
 * $dataProvider
 */

use \yii\helpers\Html;
use yii\helpers\ArrayHelper;

$dataProvider->sort = [
            'attributes' => [
                'create_date',
                'pointing_id',
                'status',
            ],
            'defaultOrder' => ['create_date' => SORT_DESC],
];

$labels = ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
rsort($labels);
$this->title = implode(' | ', $labels) . ' | '. Yii::$app->name;
?>

<div class="cart-view">

  <div class="col-md-12">
	<h2><span>ポイント付与</span></h2>
    <p>
        HJ / HE / HP 各代理店が当社のポイント付与機能を使って小売した履歴を表示しています
    </p>
<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'layout' => '{pager}{items}{pager}{summary}',
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
            'attribute' => 'company_id',
            'value'     => function($data){ return strtoupper($data->company->key); },
            'filter'    => ArrayHelper::map(\common\models\Company::find()->all(), 'company_id','key'),
        ],
        [
            'attribute' => 'seller',
            'format'    => 'html',
            'value'     => function($data){ return Html::a($data->seller->name, ['/customer/view','id'=>$data->seller_id]); },
        ],
        [
            'attribute' => 'customer',
            'format'    => 'html',
            'value'     => function($data){ return $data->customer ? Html::a($data->customer->name, ['/customer/view','id'=>$data->customer_id]) : null ; },
        ],
        [
            'attribute' => 'total_charge',
            'format'    => 'currency',
            'contentOptions' => ['class' => 'text-right'],
        ],
        [
            'attribute' => 'point_given',
            'format'    => 'integer',
            'contentOptions' => ['class' => 'text-right'],
        ],
        [
            'attribute' => 'note',
            'format'    => 'html'
 	    ],
        [
            'attribute' => 'staff_id',
            'format'    => 'html',
            'value'     => function($data){ return $data->staff ? $data->staff->name: null ; },
        ],
    ],
])?>

  </div>

</div>
