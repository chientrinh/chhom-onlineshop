<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CustomerGrade */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-grade-form">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    
        <?= $form->field($model, 'liquor_rate')->textInput() ?>

        <?= $form->field($model, 'goods_rate')->textInput() ?>

        <?= $form->field($model, 'remedy_rate')->textInput() ?>

        <?= $form->field($model, 'other_rate')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? '作成' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

    <?php $form->end(); ?>

    <?php if($model->hasErrors()): ?>
        <?= Html::errorSummary($model,['class'=>'alert alert-danger']) ?>
    <?php endif ?>
</div>
