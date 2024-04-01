<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product-pickcode/index.php $
 * $Id: index.php 2542 2016-05-26 07:12:10Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel  common\models\ProductPickcode
 */

?>
<div class="product-pickcode-index">

    <h1>ピックコード</h1>

    <div class="dropdown-menu-right">
        <?= Html::a("CSV表示", Url::current(['csv'=>1]), ['class' => 'btn btn-default pull-right']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'layout' => '{summary}{pager}{items}{pager}',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'ean13',
            'product_code',
            'pickcode',
            [
                'attribute' => 'model.name',
                'format'    => 'html',
                'value'     => function($data)
                {
                    if($data->model)
                        //return $data->model->className();
                    return Html::a($data->model->name, $data->model->url);
                },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template'=> '{update}',
            ],
        ],
    ]); ?>

    <p class="form-group">
    <?= Html::a('追加', ['create'], ['class' => 'btn btn-success']) ?>
    <?= Html::a('一括修正', ['batch-update'], ['class' => 'btn btn-warning pull-right']) ?>
    </p>

</div>
