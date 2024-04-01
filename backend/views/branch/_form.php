<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/branch/_form.php $
 * $Id: _form.php 1731 2015-11-01 02:07:31Z mori $
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Branch */
/* @var $form yii\widgets\ActiveForm */

$prefs = \yii\helpers\ArrayHelper::map(\common\models\Pref::find()->where(['pref_id'=>range(1,47)])->all(), 'pref_id', 'name');
array_unshift($prefs, "都道府県を選択");

$companies = \yii\helpers\ArrayHelper::map(\common\models\Company::find()->all(), 'company_id', 'name');
array_unshift($companies, "会社を選択");

?>

<div class="branch-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'company_id')->dropDownList($companies) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'zip01')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'zip02')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'pref_id')->dropDownList($prefs) ?>

    <?= $form->field($model, 'addr01')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'addr02')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'tel01')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'tel02')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'tel03')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
