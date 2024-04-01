<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedysku/_form.php $
 * $Id: _form.php 804 2015-03-19 07:31:58Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\RemedySku
 * @var $form yii\widgets\ActiveForm
 */

$types = \yii\helpers\ArrayHelper::map(\common\models\RemedySeries::find()->all(), 'series_id', 'name');
$vials = \yii\helpers\ArrayHelper::map(\common\models\RemedyVial::find()->all(), 'vial_id', 'name');

$potencies = \yii\helpers\ArrayHelper::map(\common\models\RemedyPotency::find()->all(), 'potency_id', 'name');

$model->start_date = date('Y-m-d');
?>

<div class="remedy-sku-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'series_id')->dropDownList($types) ?>

    <?= $form->field($model, 'vial_id')->dropDownList($vials) ?>
    <?= $form->field($model, 'vial_id')->dropDownList($potencies) ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <?= $form->field($model, 'start_date')->textInput() ?>

    <?= $form->field($model, 'expire_date')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
