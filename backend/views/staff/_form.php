<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/staff/_form.php $
 * $Id: _form.php 895 2015-04-17 00:40:58Z mori $
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Staff */
/* @var $form yii\widgets\ActiveForm */

$companies = \yii\helpers\ArrayHelper::map(\common\models\Company::find()->all(), 'company_id', 'name');

?>

<div class="staff-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'company_id')->dropDownList($companies) ?>

    <?= $form->field($model, 'name01')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'name02')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'expire_date')->textInput(['disabled'=>'disabled']) ?>

    <?= $form->field($model, 'update_date')->textInput(['disabled'=>'disabled']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
