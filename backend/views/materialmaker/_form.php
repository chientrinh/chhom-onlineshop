<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/materialmaker/_form.php $
 * $Id: _form.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MaterialMaker */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="material-maker-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'manager')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'zip01')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'zip02')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'pref_id')->textInput() ?>

    <?= $form->field($model, 'addr01')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'addr02')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'tel01')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'tel02')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'tel03')->textInput(['maxlength' => 255]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
