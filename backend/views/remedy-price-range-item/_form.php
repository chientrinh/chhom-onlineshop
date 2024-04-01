<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy-price-range-item/_form.php $
 * $Id: _form.php 3277 2017-04-28 10:37:29Z kawai $
 *
 * @var $this yii\web\View
 * @var $model common\models\RemedyPriceRangeItem
 * @var $form yii\widgets\ActiveForm
 */

$prange = \yii\helpers\ArrayHelper::map(\common\models\RemedyPriceRange::find()->all(), 'prange_id', 'name');
$vials  = \yii\helpers\ArrayHelper::map(\common\models\RemedyVial::find()->all(), 'vial_id', 'name');

?>

<div class="remedy-price-range-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'prange_id')->dropDownList($prange) ?>

    <?= $form->field($model, 'vial_id')->dropDownList($vials) ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <?= $form->field($model, 'start_date')->textInput() ?>

    <?= $form->field($model, 'expire_date')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
