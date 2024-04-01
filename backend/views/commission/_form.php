<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this yii\web\View
 * @var $model common\models\Commission
 * 
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/commission/_form.php $
 * $Id: _form.php 1878 2015-12-16 03:08:49Z mori $
 */

$companies = \common\models\Company::find()->all();
$companies = \yii\helpers\ArrayHelper::map($companies, 'company_id', 'name');
?>

<div class="commission-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'purchase_id')->textInput() ?>

    <?= $form->field($model, 'company_id')->dropDownList($companies) ?>

    <?= $form->field($model, 'customer_id')->textInput() ?>

    <?= $form->field($model, 'fee')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
