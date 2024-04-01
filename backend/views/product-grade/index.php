<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product-grade/index.php $
 * $Id: index.php 2286 2020-04-28 15:52:00Z kawai $
 *
 * @var $this yii\web\View
 * @var $searchModel common\models\ProductGradeSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

?>
<div class="product-grade-index">

    <h1>会員ランク別商品価格</h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
            'product_grade_id',
            [
                'attribute' => 'product_id',
                'format'    => 'raw',
                'value'     => function($data){ return $data->product ? $data->product_id.' :   '.Html::a($data->product->name, ['product/view', 'id'=>$data->product_id]) : "";},
            ],
            [
                'attribute' => 'grade_id',
                'format'    => 'raw',
                'value'     => function($data){ return \common\models\CustomerGrade::findOne($data->grade_id) ? \common\models\CustomerGrade::findOne($data->grade_id)->name : "一般"; },
            ],
            [
                'attribute' => 'price',
                'format'    => 'currency',
                'value'     => function($data){ return $data->price; },
            ],
            [
                'attribute' => 'tax',
                'format'    => 'currency',
                'value'     => function($data){ return $data->tax; },
            ],
            [
                'attribute' => 'tax_rate',
                'format'    => 'text',
                'value'     => function($data){ return $data->tax_rate.'%';},
            ],
            [
                'attribute' => 'expire_date',
                'format'    => 'datetime',
                'value'     => function($data){ return $data->expire_date; },
            ],
            [
                'attribute' => 'create_date',
                'format'    => 'datetime',
                'value'     => function($data){ return $data->expire_date; },
            ],
            [
                'attribute' => 'update_date',
                'format'    => 'datetime',
                'value'     => function($data){ return $data->expire_date; },
            ],
            [
                'class' => \yii\grid\ActionColumn::className(),
                'template' => '{view}{update}',
            ],

        ],
    ]); ?>

    <?= Html::a('追加', ['create'], ['class' => 'btn btn-success']) ?>

</div>
