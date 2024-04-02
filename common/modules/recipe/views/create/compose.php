<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/views/create/compose.php $
 * $Id: compose.php 3916 2018-06-01 07:13:51Z mori $
 *
 * $carts array of Cart
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

// View config
$this->params['body_id'] = 'Mypage';

$title = "オリジナルを追加";
$this->title = sprintf("%s | 新規作成 | 適用書 | %s", $title, Yii::$app->name);

?>

<div class="cart-view">

  <?= $this->render('_tab', ['model' => $recipe]) ?>

  <div class="col-md-9">

  <h2><span><?= $title ?></span></h2>

    <?php $form = ActiveForm::begin([
        'id'     => 'recipe-create-compose',
        'action' => ['/recipe/create/compose'],
        'method' => 'get',
        'fieldConfig' => [
            'template' => "{input}\n{error}",
            'horizontalCssClasses' => [
                'offset' => 'col-sm-offset-4',
                'error'  => '',
                'hint'   => '',
            ],
        ],
        'validateOnBlur'  => true,
        'validateOnChange'=> true,
        'validateOnSubmit'=> true,
    ])?>

<?= \common\widgets\ComplexRemedyView::widget([
    'user'  => Yii::$app->user->identity,
    'model' => $model,
    'showPrice' => false,
]) ?>

  <?php $form->end() ?>
</div>

  <div class="col-md-3">
      <?= $this->render('recipe-item-grid',['model'=>$recipe]) ?>
  </div>


</div>
