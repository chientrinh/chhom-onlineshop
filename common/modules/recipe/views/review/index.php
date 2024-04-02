<?php

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/views/review/index.php $
 * $Id: index.php 1179 2015-07-21 10:46:33Z mori $
 */

use \yii\helpers\Html;

$this->params['body_id'] = 'Mypage';

$dataProvider->sort = false;

?>

<div class="cart-view">
  <div class="col-md-3">
	<div class="Mypage-Nav">
	  <div class="inner">
		<h3>Menu</h3>
          <?= Yii::$app->controller->nav->run() ?>
	  </div>
	</div>
  </div>

  <div class="col-md-9">
	<h2><span>一覧</span></h2>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'layout'  => '{items}',
    'columns' => [
        [
            'attribute' => 'recipe_id',
            'format'    => 'html',
            'value'     => function($data)
            {
                return Html::a(sprintf('%06d', $data->recipe_id), ['view','id'=>$data->recipe_id]);
            }
        ],
        [
            'attribute' => 'create_date',
            'format'    => ['date','php:Y-m-d D H:i'],
        ],
        [
            'attribute' => 'homoeopath.name',
            'label'     => 'ホメオパス',
        ],
        [
            'attribute' => 'client.name',
            'label'     => 'クライアント',
        ],
    ],
])?>


<div class="well">
<p>またはこちらから検索できます</p>
<!--
 <?= $searchModel->getAttributeLabel('recipe_id') ?> と
 <?= $searchModel->getAttributeLabel('pw') ?> を入力してください
-->
<?php $form = \yii\bootstrap\ActiveForm::begin([
    'action' => ['/recipe/review/view'],
    'method' => 'get',
    ]) ?>

<?= $form->field($searchModel,'recipe_id')->textInput(['name'=>'id']) ?>

<?= $form->field($searchModel,'pw')->passwordInput(['name'=>'pw']) ?>

<?= Html::submitbutton("検索") ?>

<?php $form->end() ?>
</div>

  </div>


</div>
