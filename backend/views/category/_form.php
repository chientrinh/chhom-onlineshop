<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/category/_form.php $
 * $Id: _form.php 2286 2016-03-21 06:11:00Z mori $
 *
 * @var $this yii\web\View
 * @var $form yii\widgets\ActiveForm
 * @var $model common\models\Category
 */

$companies = \yii\helpers\ArrayHelper::map(\common\models\Company::find()->all(), 'company_id', 'name');

?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'vendor_id')->dropDownList($companies) ?>

    <?= $form->field($model, 'seller_id')->dropDownList($companies) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '追加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

