<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Recipe */
/* @var $form yii\widgets\ActiveForm */

$filter = [
    \common\models\Recipe::STATUS_INIT    => "発行",
    \common\models\Recipe::STATUS_PREINIT    => "仮発行",
    \common\models\Recipe::STATUS_SOLD    => "購入",
    \common\models\Recipe::STATUS_EXPIRED => "期限切れ",
    \common\models\Recipe::STATUS_CANCEL  => "キャンセル",
    \common\models\Recipe::STATUS_VOID    => "無効",
];

?>

<div class="recipe-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'homoeopath_id')->textInput() ?>

    <?= $form->field($model, 'client_id')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList($filter) ?>

    <?= $form->field($model, 'note')->textArea(['maxLength'=>true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
