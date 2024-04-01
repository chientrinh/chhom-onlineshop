<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/book/index.php $
 * $Id: index.php 2736 2016-07-17 06:19:13Z mori $
 *
 * @var $this yii\web\View
 * @var $searchModel common\models\SearchBook
 * @var $dataProvider yii\data\ActiveDataProvider
 */

?>
<div class="book-index">

    <p class="pull-right">
        <?= Html::a('CSV', \yii\helpers\Url::current(['format'=>'csv']),['class'=>'btn btn-default']) ?>
    </p>

    <h1>書誌</h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'product_id',
            [
                'attribute' => 'product.name',
                'format'    => 'raw',
                'value'     => function($data){ return Html::a($data->product->name, ['view','id'=>$data->product_id]); },
            ],
            'product.kana',
            'author',
            'translator',
            'page',
            'pub_date',
            //'publisher',
            //'format_id',
            'isbn',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
