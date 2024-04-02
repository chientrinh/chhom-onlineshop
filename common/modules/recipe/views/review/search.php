<?php

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/views/review/search.php $
 * $Id: search.php 3825 2018-02-02 03:31:15Z kawai $
 */

use \yii\helpers\Html;

$title = Yii::$app->controller->crumbs[$this->context->action->id]['label'];

$this->params['body_id']        = 'Mypage';

?>

<div class="cart-view">

  <div class="col-md-12">

<div class="well">

<!--
 <?= $searchModel->getAttributeLabel('recipe_id') ?> と
 <?= $searchModel->getAttributeLabel('pw') ?> を入力してください
-->

<?php $form = \yii\bootstrap\ActiveForm::begin([
    'action' => ['/recipe/review/view'],
    'method' => 'get',
    ]) ?>

<?= $form->field($searchModel,'recipe_id')->textInput(['name'=>'id','autocomplete' => 'off']) ?>

<?= $form->field($searchModel,'pw')->textInput(['name'=>'pw','autocomplete' => 'off']) ?>
<?= $form->field($searchModel,'pw')->passwordInput(['name'=>'pw','autocomplete' => 'new-password', 'style'=>'display:none;', 'disabled'=>'disabled'])->label(false) ?>
<?= Html::submitbutton("検索") ?>
　　　　豊受モールで作成された適用書のみ検索できます。ご不明な点がございましたら、【お問合せ】をクリックして頂き、お問合せフォーム（member@toyouke.com 宛）よりお問い合わせ下さい。
<?php $form->end() ?>
</div>

  </div>


</div>
