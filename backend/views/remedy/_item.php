<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy/_item.php $
 * $Id: _item.php 986 2015-05-03 08:59:46Z mori $
 */

$models = \common\models\RemedyStock::findAll([
    'remedy_id'=>$remedy->remedy_id,
    'potency_id'=>$potency->potency_id,
]);
$model = $models[0];
?>
<div class="panel panel-default">
  <div class="panel-heading">
       <h3 class="panel-title"><?= Html::a($potency->name,['remedy-potency/view','id'=>$potency->potency_id],['title'=> "価格帯:".$model->prange->name]) ?></h3>
  </div>
  <div class="panel-body">

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => $models,
            'sort' => [
                'attributes'   => ['vial_id', 'prange_id'],
                'defaultOrder' => ['vial_id' => SORT_ASC],
            ],
            'pagination' => false,
        ]),
        'layout'  => '{items}',
        'tableOptions' => ['class'=>'table table-condensed'],
        'columns' => [
            [
                'attribute'=> 'vial',
                'format'   => 'html',
                'value'    => function($data){ return Html::a($data->vial->name,['remedy-vial/view','id'=>$data->vial_id]); },
                'contentOptions' => ['class'=>'col-md-8'],
            ],
            [
                'attribute'=> 'price',
                'format'   => 'html',
                'value'    => function($data){ return sprintf("&yen;%s",number_format($data->price)); },
                'contentOptions' => ['style'=>'text-align:right','class'=>'col-md-1'],
            ],
            [
                'attribute' => 'in_stock',
                'value'     => function($data){ return ($data->in_stock) ? "あり" : "なし"; },
                'contentOptions' => ['style'=>'text-align:right','class'=>'col-md-1'],
            ],
            // [
            //     'format' => 'raw',
            //     'value' => function($data){
            //         if($data->in_stock)
            //             return Html::a("欠品処理", ['update', 'id' => $data->remedy_id], ['class' => 'btn btn-default']); 
            //         else
            //             return Html::a("欠品解除", ['update', 'id' => $data->remedy_id], ['class' => 'btn btn-danger']);
            //     },
            //     'contentOptions' => ['style'=>'text-align:right','class'=>'col-md-1'],
            //            ],
        ],
    ]); ?>


  </div>
</div>

