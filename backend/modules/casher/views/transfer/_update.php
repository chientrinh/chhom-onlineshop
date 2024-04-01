<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/transfer/_update.php $
 * $Id: _update.php 2199 2016-03-05 02:40:59Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\TransferItem
 */

?>
<?php $form = \yii\bootstrap\ActiveForm::begin([
    'class' => 'transfer-item-form',
    'id'    => 'transfer-item-form-'.$model->item_id,
    'action'=> 'update-item',
    'method'=> 'get',
    'fieldConfig' => [
        'template' => '{input}',
    ]
]); ?>

<?= Html::hiddenInput('id',$model->item_id) ?>
<?= Html::hiddenInput('target','qty_shipped') ?>

<?= $form->field($model,'qty_shipped')->textInput(['name'=>'value','style'=>'font-weight:bold;font-size:150%']) ?>

<?php $form->end(); ?>
