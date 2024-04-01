<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product/index.php $
 * $Id: index.php 2067 2016-02-11 09:17:40Z mori $
 *
 * @var $this yii\web\View
 * @var $searchModel common\models\ProductSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

?>

<div class="product-description-index">

    <h1>商品 / 補足</h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'layout'  => '<span class="pull-right">{summary}</span>{pager}{items}{pager}',
        'columns' => [
            'desc_id',
            [
                'attribute' => 'product_id',
                'format'    => 'html',
                'value'     => function($data){
                    if($p = $data->product)
                        return Html::a($p->name, ['product/view','id'=>$p->product_id]);
                }
            ],
            'title',
            'body',
            [
                'class' => \yii\grid\ActionColumn::className(),
                'template' => '{update}',
            ],
        ],
    ]); ?>

</div>
