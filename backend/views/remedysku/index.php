<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedysku/index.php $
 * $Id: index.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchRemedySku */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Remedy Skus';
$this->params['breadcrumbs'][] = $this->title;
$dataProvider->pagination->pageSize=100;
?>
<div class="remedy-sku-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Remedy Sku', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'sku_id',
            [
                'attribute'=> 'series_id',
                'value'    => function($data){ return $data->series->name; },
            ],
            [
                'attribute'=> 'vial_id',
                'value'    => function($data){ return $data->vial->name; },
            ],
            [
                'attribute'=>'price',
            ],
            //'start_date:date',
            // 'expire_date',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
