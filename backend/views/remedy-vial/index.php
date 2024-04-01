<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy-vial/index.php $
 * $Id: index.php 804 2015-03-19 07:31:58Z mori $
 *
 * @var $this yii\web\View
 * @var $searchModel common\models\SearchRemedyVial
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = 'Remedy Vials';
$this->params['breadcrumbs'][] = $this->title;
$dataProvider->sort->attributes[] = 'unit.name';
?>
<div class="remedy-vial-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Remedy Vial', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'name',
                'format'    => 'raw',
                'value'     => function($data){ return Html::a($data->name,['view','id'=>$data->vial_id]); },
            ],
            [
                'attribute' => 'volume',
                'contentOptions' => ['style'=>'text-align:right'],
            ],
            [
                'attribute' => 'unit_id',
                'value'     => function($data){ return $data->unit->name; },
            ],

        ],
    ]); ?>

</div>
