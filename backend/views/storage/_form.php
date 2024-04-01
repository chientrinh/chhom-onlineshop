<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/storage/_form.php $
 * $Id: _form.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Storage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="storage-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'src_id')->textInput() ?>

    <?= $form->field($model, 'dst_id')->textInput() ?>

    <?= $form->field($model, 'staff_id')->textInput() ?>

    <?= $form->field($model, 'ship_date')->textInput() ?>

    <?= $form->field($model, 'pick_date')->textInput() ?>

    <?= $form->field($model, 'create_date')->textInput() ?>

    <?= $form->field($model, 'update_date')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
