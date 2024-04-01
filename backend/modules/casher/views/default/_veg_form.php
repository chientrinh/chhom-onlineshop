<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/_veg_form.php $
 * $Id: _veg_form.php 3504 2017-07-25 11:23:46Z kawai $
 *
 * $model common\models\Vegetable;
 */
use \yii\helpers\Html;

$quantity = range(0,99);
\yii\helpers\ArrayHelper::remove($quantity, 0);
?>

<?php $form = \yii\bootstrap\ActiveForm::begin([
    'id'     => $model->veg_id,
    'action' => 'apply',
    'method' => 'get',
    'layout' => 'inline',
    'class'  => 'product-add-form',
]) ?>

    <div class="col-md-10 col-sm-8 col-xs-10">
        <div class="input-group">
            <?= Html::dropDownList('qty', 1, $quantity, $options = ['maxlangth'=>3,'class'=>'pull-left form-control','style'=>'width:inherit'])?>    
            <?= Html::textInput('price', null, ['size'=>5,'class'=>"form-control js-zenkaku-to-hankaku",'style'=>'width:inherit'])?>
            <span class="input-group-addon">円</span>
        </div>
    </div>
    <?= Html::hiddenInput('vid', $model->veg_id) ?>
    <?= Html::hiddenInput('target', 'veg') ?>

    <?= Html::submitButton('追加',['class'=>'btn btn-sm btn-warning','title'=>'追加']) ?>

<?php $form->end() ?>
