<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy-price-range/index.php $
 * $Id: index.php 1157 2015-07-15 13:01:02Z mori $
 *
 * @var $this yii\web\View
 * @var $searchModel common\models\SearchRemedyPriceRange
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = 'Remedy Price Ranges';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="remedy-price-range-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Remedy Price Range', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'format'    => 'raw',
                'value'     => function($data){ return Html::a($data->name,['view','id'=>$data->prange_id]); },
            ],
        ],
    ]); ?>

</div>
