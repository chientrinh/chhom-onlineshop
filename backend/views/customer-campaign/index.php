<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Customer Campaign';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-grade-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'customer_id',
            [
                'attribute' => 'name',
                'format'    => 'html',
                'value'     => function($data){ return $data->customer->name; },
            ],
            'create_date'
        ],
    ]); ?>
</div>
