<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/staff-role/_form.php $
 * $Id: _form.php 1976 2016-01-13 08:04:30Z mori $
 *
 * @var $this yii\web\View
 * @var $model app\models\Staff
 */

$staffs   = \yii\helpers\ArrayHelper::map(\backend\models\Staff::find()->all(), 'staff_id', 'name');
$roles    = \yii\helpers\ArrayHelper::map(\backend\models\Role::find()->all(),  'role_id',  'description');
$branches = \yii\helpers\ArrayHelper::map(\common\models\Branch::find()->all(), 'branch_id','name');

?>

<div class="staff-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'staff_id')->dropDownList($staffs) ?>

    <?= $form->field($model, 'role_id')->dropDownList($roles) ?>

    <?= $form->field($model, 'branch_id')->dropDownList($branches) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
