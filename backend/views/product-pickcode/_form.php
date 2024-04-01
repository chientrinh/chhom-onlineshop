<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProductPickcode */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-pickcode-form">

    <?php $form = ActiveForm::begin(['method'=>'get']); ?>

    <?= $form->field($model, 'ean13')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'product_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pickcode')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '追加' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

        <?php if(!$model->isNewRecord): ?>
            <?= Html::a('削除', ['delete','id'=>$model->pickcode], ['class' => 'btn btn-danger pull-right']) ?>
        <?php endif ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?= Html::errorSummary($model,['class'=>'alert alert-danger']) ?>

</div>
