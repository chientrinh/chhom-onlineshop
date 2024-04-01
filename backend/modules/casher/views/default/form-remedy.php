<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/form-remedy.php $
 * $Id: $
 *
 * $model \common\models\ProductMaster
 */
use \yii\helpers\Html;
use \yii\helpers\Url;

$quantity = range(0,99);
\yii\helpers\ArrayHelper::remove($quantity, 0);
?>

<?php $form = \yii\bootstrap\ActiveForm::begin([
    'action' => 'apply',
    'method' => 'get',
    'layout' => 'inline',
    'class'  => 'product-add-form',
]); ?>

    <?= Html::dropDownList('qty', 1, $quantity, $options = ['maxlangth'=>3,'class'=>'pull-left form-control','style'=>'width:inherit'])?>
    &nbsp;
    <?= Html::hiddenInput('target', $target) ?>
    <?= Html::hiddenInput('rid', $model->remedy_id ) ?>
    <?= Html::hiddenInput('pid', $model->potency_id) ?>
    <?= Html::hiddenInput('vid', $model->vial_id   ) ?>

    <?= Html::submitButton('追加',['class'=>'btn btn-sm btn-warning']) ?>

<?php $form->end() ?>

