<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/category/index.php $
 * $Id: index.php 3223 2017-03-18 05:18:22Z naito $
 *
 * @var $this yii\web\View
 * @var $searchModel common\models\CategorySearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$companies = \common\models\Company::find()->all();
$companies = ArrayHelper::map($companies, 'company_id','name');
?>
<div class="category-index">

    <h1>カテゴリー</h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
            'category_id',
            [
                'attribute' => 'name',
                'format'    => 'raw',
                'value'     => function($data){ return Html::a($data->name, ['view', 'id'=>$data->category_id]); },
            ],
            [
                'attribute' => 'vendor_id',
                'format'    => 'text',
                'value'     => function($data){ return $data->vendor->name; },
                'filter'    => $companies,
            ],
            [
                'attribute' => 'seller_id',
                'format'    => 'text',
                'value'     => function($data){ return $data->seller->name; },
                'filter'    => $companies,
            ],
        ],
    ]); ?>

    <?= Html::a('追加', ['create'], ['class' => 'btn btn-success']) ?>

</div>
