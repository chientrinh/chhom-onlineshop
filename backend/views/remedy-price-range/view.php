<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy-price-range/view.php $
 * $Id: view.php 804 2015-03-19 07:31:58Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\RemedyPriceRange
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Remedy Price Ranges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="remedy-price-range-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->prange_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->prange_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'prange_id',
            'name',
        ],
    ]) ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $provider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'vial_id',
            [
                'attribute' => 'vial',
                'format'    => 'raw',
                'value'     => function($data){ return Html::a($data->vial->name,['remedy-vial/view','id'=>$data->vial->vial_id]); },
            ],
            [
                'attribute'=>'vial.volume',
                'value'    => function($data){return sprintf("%d %s", $data->vial->volume, $data->vial->unit->name); },
            ],
            'price',
        ],
    ]); ?>

</div>
