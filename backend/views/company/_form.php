<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/company/_form.php $
 * $Id: _form.php 1356 2015-08-23 10:47:34Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Company
 * @var $form yii\widgets\ActiveForm
 */
$prefs = \yii\helpers\ArrayHelper::map(\common\models\Pref::find()->all(), 'pref_id', 'name');

?>

<div class="company-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'key')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'manager')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'zip01')->textInput(['maxlength' => 3]) ?>

    <?= $form->field($model, 'zip02')->textInput(['maxlength' => 4]) ?>

    <?= $form->field($model, 'pref_id')->dropDownList($prefs) ?>

    <?= $form->field($model, 'addr01')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'addr02')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'tel01')->textInput(['maxlength' => 6]) ?>

    <?= $form->field($model, 'tel02')->textInput(['maxlength' => 6]) ?>

    <?= $form->field($model, 'tel03')->textInput(['maxlength' => 4]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
