<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/agency-rating/index.php $
 * $Id: index.php 1802 2015-11-13 16:11:19Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$companies = \yii\helpers\ArrayHelper::map(\common\models\Company::find()->where(['company_id'=>[2,3,4]])->all(), 'company_id', 'key');
?>
<div class="agency-rating-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'customer_id',
                'format'    => 'html',
                'value'     => function($data){ return $data->customer->name; },
            ],
            [
                'attribute' => 'company_id',
                'format'    => 'html',
                'value'     => function($data){ return $data->company->key; },
                'filter'    => $companies,
            ],
            'discount_rate:integer',
            [
                'attribute' => 'start_date',
                'format'    => ['date','php:Y-m-d'],
            ],
            [
                'attribute' => 'end_date',
                'format'    => ['date','php:Y-m-d'],
            ],
            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{view}{update}',
            ],
        ],
    ]); ?>

    <p>
        <?= Html::a('追加', ['create','id'=>''], ['class' => 'btn btn-success']) ?>
    </p>

</div>
