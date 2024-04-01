<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/membercode/_form.php $
 * $Id: _form.php 3821 2018-01-26 09:15:58Z naito $
 *
 * @var $this yii\web\View
 * @var $model common\models\Membercode
 * @var $form yii\widgets\ActiveForm
 */

$directives = ['','webdb20','webdb18','ecorange','eccube'];
$directives = array_combine($directives, $directives);
?>

<div class="membercode-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'code')->textInput(['length' => 10]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'customer_id')->textInput() ?>

    <?= $form->field($model, 'directive')->DropDownList($directives) ?>

    <?= $form->field($model, 'migrate_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
