<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\models\CustomerInfoWeight;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/customer-info/index.php $
 * $Id: index.php 2738 2016-07-17 08:47:41Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->params['breadcrumbs'][] = ['label'=>'顧客','url'=>['/customer']];
$this->params['breadcrumbs'][] = ['label'=>'付記','url'=>['/customer-info']];

$dataProvider->pagination->pageSize = 50;

$weights = CustomerInfoWeight::find()->all();
$weights = ArrayHelper::map($weights, 'weight_id', 'name');

?>
<div class="customer-membership-index">

    <h1>付記</h1>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'weight_id',
                'value'     => function($data){ return ($w = $data->weight) ? $w->name : null; },
                'filter'    => $weights,
            ],
            [
                'attribute' => 'customer_id',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->customer->name, ['/customer/view','id'=>$data->customer_id]); },
            ],
            [
                'attribute' => 'content',
                'format'    => 'ntext',
                'value'     => function($data){ return yii\helpers\StringHelper::truncate($data->content, 100); },
            ],
            
            'create_date',
            'update_date',
            [
                'attribute' => 'created_by',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->creator->name, ['/staff/view','id'=>$data->creator->staff_id]); },
            ],
            [
                'attribute' => 'updated_by',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->creator->name, ['/staff/view','id'=>$data->creator->staff_id]); },
            ],
            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{update}',
            ],
        ],
    ]); ?>

</div>
