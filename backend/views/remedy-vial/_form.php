<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy-vial/_form.php $
 * $Id: _form.php 804 2015-03-19 07:31:58Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\RemedyVial
 * @var $form yii\widgets\ActiveForm
 */

$units = \yii\helpers\ArrayHelper::map(\common\models\Unit::find()->all(), 'unit_id', 'name');

?>

<div class="remedy-vial-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'volume')->textInput() ?>

    <?= $form->field($model, 'unit_id')->dropDownList($units) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
