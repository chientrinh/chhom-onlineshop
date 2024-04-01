<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/role/_form.php $
 * $Id: _form.php 1505 2015-09-18 13:50:50Z mori $
 *
 * @var $this yii\web\View
 * @var $model backend\models\Role
 */
?>

<div class="role-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
