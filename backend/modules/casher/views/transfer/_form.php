<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/transfer/_form.php $
 * $Id: _form.php 2058 2016-02-11 02:16:25Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\TransferItem
 */
$jscode = "
$('input').change(function(){
    $(this).submit();
    return false;
})";

$this->registerJs($jscode);
?>
<?php $form = \yii\bootstrap\ActiveForm::begin([
    'class' => 'transfer-item-form',
    'id'    => 'form-qty-shipped-'.$model->item_id,
    'action'=> 'update-item',
    'fieldConfig' => [
        'template' => '{input}',
    ]
]); ?>

<?= Html::hiddenInput('id',$model->item_id) ?>
<?= Html::hiddenInput('target','qty_shipped') ?>

<?= $form->field($model,'qty_shipped')->textInput(['name'=>'value','style'=>'font-weight:bold;font-size:150%']) ?>

<?php $form->end(); ?>

