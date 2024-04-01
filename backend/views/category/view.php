<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/category/view.php $
 * $Id: view.php 2992 2016-10-19 07:37:38Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Category
 */

$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view','id'=>$model->category_id]];
?>
<div class="category-view">

    <p class="pull-right">
        <?= Html::a('編集', ['update', 'id' => $model->category_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('',['view','id'=>$model->category_id -1],['class'=>'btn btn-sm btn-default glyphicon glyphicon-chevron-left'])?>
        <?= Html::a('',['view','id'=>$model->category_id +1],['class'=>'btn btn-sm btn-default glyphicon glyphicon-chevron-right'])?>
    </p>

    <h1><?= Html::encode($model->name) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'category_id',
            [
                'attribute' => 'vendor_id',
                'format'    => 'raw',
                'value'     => Html::a($model->vendor->name, ['company/view', 'id'=>$model->vendor->company_id]),
            ],
            [
                'attribute' => 'seller_id',
                'format'    => 'raw',
                'value'     => Html::a($model->seller->name, ['company/view', 'id'=>$model->seller->company_id]),
            ],
        ],
    ]) ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getProducts(),
            'sort'  => ['defaultOrder' => ['product_id'=>SORT_DESC]],
        ]),
        'columns' => [
            'product_id',
            'code',
            [
                'attribute' => 'name',
                'format'    => 'html',
                'value'     => function($data)
                {
                    return Html::a($data->name, ['product/view','id'=>$data->product_id]);
                },
            ],
            'kana',
            'price:currency',
            [
                'attribute' => 'start_date',
                'format'    => ['date', 'php:Y-m-d'],
            ],
            [
                'attribute' => 'expire_date',
                'format'    => ['date', 'php:Y-m-d'],
            ],
            [
                'attribute' => 'restrict_id',
                'value'     => function($data)
                {
                    return $data->restriction->name;
                }
            ],
            'in_stock:boolean',
        ],
        'rowOptions' => function ($model, $key, $index, $grid)
        {
            if($model->isExpired())
                return ['class'=> 'text-danger'];
        }
    ]) ?>
</div>
