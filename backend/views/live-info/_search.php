<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/live-info/_search.php $
 * $Id: _search.php 804 2020-04-28 12:31:58Z kawai $
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\StreamingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="live-info-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'info_id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'coupon_name') ?>

    <?= $form->field($model, 'coupon_code') ?>

    <?= $form->field($model, 'option_name') ?>


    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
