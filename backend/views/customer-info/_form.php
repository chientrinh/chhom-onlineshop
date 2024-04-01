<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\models\CustomerInfoWeight;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/customer-info/_form.php $
 * $Id: _form.php 2737 2016-07-17 08:06:54Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\CustomerInfo
 */

$weights = CustomerInfoWeight::find()->all();
$weights = ArrayHelper::map($weights, 'weight_id', 'name');

?>

<div class="customer-info-form">

    <?php $form = \yii\widgets\ActiveForm::begin(); ?>

    <?= $form->field($model->customer, 'name')->textInput(['disabled'=>'disabled']) ?>

    <?= $form->field($model, 'content')->textArea() ?>

    <?= $form->field($model, 'weight_id')->dropDownList($weights) ?>

    <div class="col-md-12 form-group">
        <?= Html::submitButton($model->isNewRecord ? '追加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php $form->end(); ?>

</div>
