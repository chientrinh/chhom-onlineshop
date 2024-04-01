<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/_reduce.php $
 * $Id: _reduce.php 3709 2017-10-26 09:43:59Z kawai $
 *
 * $model item of PurchaseForm::items
 */

use \yii\helpers\Html;
// var_dump($purchase);exit;
?>

<p>
    <?= Yii::$app->formatter->asCurrency($model->price) ?>
</p>
<?php if(0): ?>
<!-- /* NOT WORKING */ -->
<?= Html::a('&minus;'. $model->getDiscountRate() . '%','#',['class'=>'reduce-per-txt','id'=>'per-'.$key]) ?>
<?php elseif($model->getDiscountRate()) : ?>
    <span class="text-muted">&minus;<?= $model->getDiscountRate() ?>%</span>
<?php endif ?>
<?php //var_dump($model->campaign_id); ?>
  <?= Html::a('&yen;&minus;'. $model->getDiscountAmount() ,'#',['class'=>'reduce-yen-txt','id'=>'yen-'.$key]) ?>
<?= Html::beginForm(['apply', 'target'=>'reduce'],
                    'get', [
                        'id'   => sprintf('cart-reduce-%d', $key),
                        'name' => sprintf('cart-reduce-%d', $key)
                    ]) ?>

<?= Html::input('hidden', 'seq', $key) ?>

<?php if(0): ?>
<!-- /* NOT WORKING */ -->
<div class="input-group" id="reduce-grp-per-<?= $key ?>" style="display:none">
  <span class="input-group-addon reduce-per">%</span>

  <?= Html::input('text', 'per', $model->discount_rate, [
      'size'     => 2,
      'onChange' => 'this.form.submit()',
      'class'    => 'form-control js-zenkaku-to-hankaku',
      'aria-described-by' => 'reduce-per',
  ]) ?>
</div>
<?php endif ?>

<div class="input-group pull-right" id="reduce-grp-yen-<?= $key ?>" style="display:none">
  <span class="input-group-addon reduce-yen">&yen;</span>

  <?= Html::input('text', 'yen', $model->discount_amount, [
      'size'     => 4,
      'onChange' => 'this.form.submit()',
      'class'    => 'form-control js-zenkaku-to-hankaku text-right',
      'aria-described-by' => 'reduce-yen',
  ]) ?>

</div>
</div>
<?= Html::endForm() ?>
