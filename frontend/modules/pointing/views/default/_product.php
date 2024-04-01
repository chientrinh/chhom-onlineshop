<?php

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/pointing/views/default/_product.php $
 * $Id: _product.php 3627 2017-09-30 08:15:28Z kawai $
 */

use \yii\helpers\Html;

?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'layout'  => '{items}{pager}{summary}',
    'columns' => [
        [
            'label' => 'バーコード',
            'attribute' => 'ean13',
            'filterOptions'  => ['title'=>'部分一致 による検索が可能です'],
            'headerOptions'  => ['title'=>'コード順に並べ替えます'],
        ],
        [
            'attribute' => 'name',
            'label' => '品名',
        ],
        [
            'class'=> 'yii\grid\ActionColumn',
            'template' => '{apply}',
            'buttons' =>[
                'apply'    => function ($url, $model, $key) { return Html::a('決定', ['apply','target'=>'product','barcode'=>$model->ean13],['class'=>'btn btn-xs btn-success']); },

//                'apply'    => function ($url, $model, $key) { return Html::a('決定', ['apply','target'=>'product','id'=>$model->product_id],['class'=>'btn btn-xs btn-success']); },
            ],
        ],
        [
            'label' => '価格',
            'attribute'=> 'price',
            'format'   => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'headerOptions'  => ['title'=>'価格順に並べ替えます'],
        ],
//        [
//            'label' => 'かな',
//            'attribute'=>'kana',
//            'contentOptions' => ['class'=>'small'],
//            'filterOptions'  => ['title'=>'ローマ字、ひらがな による検索が可能です'],
//            'headerOptions'  => ['title'=>'よみがな順に並べ替えます'],
//        ],
    ],
])?>

