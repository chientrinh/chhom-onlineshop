<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product/index.php $
 * $Id: index.php 2736 2016-07-17 06:19:13Z mori $
 *
 * @var $this yii\web\View
 * @var $searchModel common\models\ProductSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */


$categories = ArrayHelper::map(\common\models\Category::find()->all(), 'category_id', function($model){ return sprintf('%s:%s', strtoupper($model->seller->key), $model->name); });
asort($categories);

$this->params['breadcrumbs'][] = [
        'label' => 'おすすめ商品',
        'url'   => ['recommend']
    ];

?>

<div class="product-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => ('search' == Yii::$app->controller->action->id) ? null : $searchModel,
        'pager'        => ['maxButtonCount' => 20],
        'layout'       => '{summary}{pager}{items}{pager}',
        'columns'      => [
            [
                'attribute' => 'recommend_seq',
                'format'    => 'html'
            ],
            'product_id',
            [
                'attribute' => 'category_id',
                'format'    => 'raw',
                'value'     => function($data){ if($c = $data->category) return $c->name; },
                'filter'    => $categories,
            ],
            'code',
            [
                'attribute' => 'name',
                'format'    => 'raw',
                'value'     => function($data){ return Html::a($data->name, ['view','id' => $data->product_id]); },
            ],
            'kana',
            'price',
            [
                'attribute' => 'in_stock',
                'format'    => 'html',
                'value'     => function($data){ return $data->in_stock ? 'OK' : Html::tag('span','NG',['class'=>'btn btn-danger']); },
                'filter'    => [1 => 'OK', 0 => 'NG'],
            ],
            [
                'attribute' => 'start_date',
                'format'    => 'date',
            ],
            [
                'attribute' => 'expire_date',
                'format'    => 'date',
            ],
        ],
        'rowOptions' => function($model)
        {
            if($model->isExpired())
                return ['class' => 'text-danger'];
        },
    ]); ?>
</div>
