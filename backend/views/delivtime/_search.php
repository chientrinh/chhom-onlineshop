<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/delivtime/_search.php $
 * $Id: _search.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchDelivTime */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="deliv-time-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'dtime_id') ?>

    <?= $form->field($model, 'deliveror_id') ?>

    <?= $form->field($model, 'time') ?>

    <?= $form->field($model, 'name') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
