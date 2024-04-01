<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use \common\models\StreamingBuy;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product-grade/view.php $
 * $Id: view.php 2992 2020-04-28 15:57:38Z kawai $
 *
 * @var $this yii\web\View
 * @var $model common\models\ProductGrade
 */

$this->params['breadcrumbs'][] = ['label' => '会員ランク別価格 '.$model->product_grade_id, 'url' => ['view','id'=>$model->product_grade_id]];
?>
<div class="product-grade-view">

    <p class="pull-right">
        <?= Html::a('編集', ['update', 'id' => $model->product_grade_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('',['view','id'=>$model->product_grade_id -1],['class'=>'btn btn-sm btn-default glyphicon glyphicon-chevron-left'])?>
        <?= Html::a('',['view','id'=>$model->product_grade_id +1],['class'=>'btn btn-sm btn-default glyphicon glyphicon-chevron-right'])?>
    </p>

    <h1><?= '会員ランク別商品価格　'.$model->product_grade_id ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'product_grade_id',
            [
                'attribute' => 'product_id',
                'format'    => 'raw',
                'value'     => $model->product ? $model->product_id.' :   '.Html::a($model->product->name, ['product/view', 'id'=>$model->product_id]) : "",
            ],
            [
                'attribute' => 'grade_id',
                'format'    => 'raw',
                'value'     => \common\models\CustomerGrade::findOne($model->grade_id) ? \common\models\CustomerGrade::findOne($model->grade_id)->name : "一般",
            ],
            [
                'attribute' => 'price',
                'format'    => 'currency',
                'value'     => $model->price,
            ],
            [
                'attribute' => 'tax',
                'format'    => 'currency',
                'value'     => $model->tax,
            ],
            [
                'attribute' => 'tax_rate',
                'format'    => 'text',
                'value'     => $model->tax_rate.'%',
            ],
            [
                'attribute' => 'expire_date',
                'format'    => 'datetime',
                'value'     => $model->expire_date,
            ],
            [
                'attribute' => 'create_date',
                'format'    => 'datetime',
                'value'     => $model->create_date,
            ],
            [
                'attribute' => 'update_date',
                'format'    => 'datetime',
                'value'     => $model->update_date,
            ],

        ],
    ]) ?>

</div>
