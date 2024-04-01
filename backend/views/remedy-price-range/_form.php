<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy-price-range/_form.php $
 * $Id: _form.php 804 2015-03-19 07:31:58Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\RemedyPriceRange
 * @var $form yii\widgets\ActiveForm
 */

?>

<div class="remedy-price-range-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
