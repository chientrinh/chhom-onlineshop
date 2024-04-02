<?php

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/views/create/product.php $
 * $Id: product.php 3916 2018-06-01 07:13:51Z mori $
 */

use \yii\helpers\Html;

$this->params['body_id'] = 'Mypage';

$title = "キットを追加";
$this->title = sprintf("%s | 新規作成 | 適用書 | %s", $title, Yii::$app->name);
?>

<div class="cart-view">

  <?= $this->render('_tab', ['model' => $recipe]) ?>

  <div class="col-md-9">

  <h2><span><?= $title ?></span></h2>

  <?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'tableOptions' => ['class'=>'table table-condensed table-striped'],
    'layout' => '{items}{pager}',
    'columns' => [
        [
            'class'=> 'yii\grid\ActionColumn',
            'template' => '{apply}',
            'buttons' =>[
                'apply'    => function ($url, $model, $key) {
                  return Html::a('✔', ['add','target'=>'product','id'=>$model->product_id],['class'=>'btn btn-xs btn-success','title'=>'カートに追加']);
                },
            ],
        ],

        'name',
        [
            'attribute'=>'kana',
            'contentOptions' => ['class'=>'small'],
        ],
    ],
])?>

  </div>

  <div class="col-md-3">
      <?= $this->render('recipe-item-grid',['model'=>$recipe]) ?>
  </div>

</div>
