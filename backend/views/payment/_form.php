<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/*
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/payment/_form.php $
 * $Id: _form.php 1981 2016-01-14 06:04:04Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Payment
 * @var $form yii\widgets\ActiveForm
 */
?>

<div class="payment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if($model->isNewRecord): ?>
    <?= $form->field($model, 'payment_id')->textInput() ?>
    <?php endif ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'delivery')->radioList([0=>'はい',1=>'いいえ']) ?>

    <?= $form->field($model, 'datetime')->radioList([0=>'はい',1=>'いいえ']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
