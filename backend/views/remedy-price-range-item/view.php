<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy-price-range-item/view.php $
 * $Id: view.php 3277 2017-04-28 10:37:29Z kawai $
 *
 * @var $this yii\web\View
 * @var $model common\models\RemedyPriceRangeItem
 */


?>
<div class="remedy-price-range-item-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'prange_id',
                'format'    => 'raw',
                'value'     => $model->prange->name,
            ],
            [
                'attribute' => 'vial_id',
                'format'    => 'raw',
                'value'     => $model->vial->name,
            ],
            [
                'attribute' => 'price',
                'format'    => 'raw',
                'value'     => sprintf("&yen;%d",$model->price),
            ],
            'start_date:date',
            'expire_date:date',
        ],
    ]) ?>

    <?php if (Yii::$app->user->identity->hasRole(['wizard'])) : ?>
        <p class="text-right">
            <?= Html::a('更新', ['update', 'prange_id' => $model->prange_id, 'vial_id' => $model->vial_id, 'price' => $model->price, 'start_date' => $model->start_date], ['class' => 'btn btn-primary']) ?>
        </p>
    <?php endif; ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => $model->stocks,
            'pagination'=>['pagesize' => 100],
            'sort' => [
            ],
        ]),
        'caption' => '既製品',
        'columns' => [
            [
                'attribute' => 'barcode',
                'format' => 'html',
                'value'  => function($data)
                {
                    return Html::a($data->barcode, [
                        '/remedy-stock/view',
                        'remedy_id'  => $data->remedy_id,
                        'potency_id' => $data->potency_id,
                        'vial_id'    => $data->vial_id,
                    ]);
                },
            ],
            'pickcode',
            'remedy.name',
            'potency.name',
            'in_stock:boolean',
        ],
    ]) ?>

</div>
