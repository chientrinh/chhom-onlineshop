<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/compose.php $
 * $Id: compose.php 3496 2017-07-20 10:04:05Z kawai $
 *
 * $carts array of Cart
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$csscode = "
.initial > li {
  display: inline;
  list-style: none;
  height: 30px;
}
.initial > li > a {
  display: block;
  float: left;
  width: 30px;
  padding: 6px 10px 6px 10px;

  border: 1px solid #CCC;
}
.Detail-Total {
  border: 5px solid #CCC;
  border-radius: 4px;
  -moz-border-radius: 4px;
  -webkit-border-radius: 4px;
}
.Detail-Total .inner {
    padding: 15px;
    border: 1px solid #C0C0C0;
 }
.Detail-Total h5 {
    font-weight: bold !important;
 }
.Detail-Total h5:before {
    margin-right:.25em;
    color: #507EA3;
    content:'●';
}
.Detail-Total h5 .btn {
    float: right;
    font-size: 85%;
    margin: -5px 0 0 5px;
    padding: 4px 5px 2px 6px;
    letter-spacing: 0.1em;
    border-radius: 2px;
    -moz-border-radius: 2px;
    -webkit-border-radius: 2px; }
";
if('app-backend' == Yii::$app->id)
    $this->registerCss($csscode);
?>

<div class="cart-default-index">
  <div class="col-md-12">
    <?= $this->render('__nav') ?>

    <div id="cart-items" class="col-md-2">
        <?php if($this->context->module->purchase->items):
        $items = $this->context->module->purchase->items; ?>
        <?= \backend\modules\casher\widgets\CartContentGrid::widget(['items' => $items]) ?> 
        <?php endif ?>
    </div>

    <div class="col-md-10">
      <?= $this->render('__tabs',[
          'company' => \common\models\Company::PKEY_HJ,
      ]) ?>

      <?php $form = ActiveForm::begin([
          'id' => 'form-compose',
          'action' => ['compose'],
          'method' => 'get',
          'validateOnBlur'  => true,
          'validateOnChange'=> true,
          'validateOnSubmit'=> true,
          'fieldConfig' => [
              'template' => "{input}\n{error}",
              'horizontalCssClasses' => [
                  'offset' => 'col-sm-offset-4',
                  'error' => '',
                  'hint' => '',
              ],
          ],
      ])?>

      <?= \common\widgets\ComplexRemedyView::widget([
          'user'  => Yii::$app->user->identity,
          'model' => $model,
          'showPrice' => true,
      ]) ?>

      <?php $form->end() ?>
    </div>

  </div>

</div>
