<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy-vial/view.php $
 * $Id: view.php 804 2015-03-19 07:31:58Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\RemedyVial
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Remedy Vials', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="remedy-vial-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->vial_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->vial_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            [
                'label'     => '容量',
                'format'    => 'raw',
                'value'     => sprintf("%d %s", $model->volume, $model->unit->name),
            ],
        ],
    ]) ?>

<?php
$provider = new \yii\data\ArrayDataProvider([
    'allModels' => $model->remedyPriceRangeItems,
    'sort' => [
        'attributes' => ['prange_id', 'price']
    ],
    'pagination' => [
        'pageSize' => 30,
    ],
]);
?>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => $provider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'prange_id',
                'format'    => 'raw',
                'value'     => function($data){ return Html::a($data->prange->name,['remedy-price-range/view','id'=>$data->prange_id]); },
            ],
            'price',
        ],
    ]); ?>

</div>
