<?php
/*
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/_ajax.php $
 * $Id: _ajax.php 3934 2018-06-20 03:57:25Z mori $
 */

use \yii\helpers\Html;
?>

<?= Html::beginForm(['apply', 'target'=>'quantity', 'operator'=>'='],
                                'get', [
                                    'id'   => sprintf('cart-item-%d', $key),
                                    'name' => sprintf('cart-item-%d', $key)
                                ])
?>
<?= Html::input('hidden', 'seq', $idx) ?>
<?= Html::input('text', 'vol', $model->qty, [
                  'id'       => sprintf('ipt-qty-%d', $key),
                  'size'     => 2,
                  'onChange' => 'this.form.submit()',
                  'class'    => 'form-control input-lg js-zenkaku-to-hankaku input-group',
                  #'tabindex' => $tabindex++,
                  'style'    => 'display:none',
              ]) ?>

<?= Html::endForm() ?>

<p class="text-center">

    <?= Html::a('&nbsp;'.$model->qty.'&nbsp;',[$this->context->action->id],['class'=>'qty-txt','style'=>'font-size:150%;font-weight:bold;','id'=>'qty-'.$key]) ?>

</p>
