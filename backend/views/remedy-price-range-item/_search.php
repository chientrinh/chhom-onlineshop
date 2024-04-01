<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy-price-range-item/_search.php $
 * $Id: _search.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SearchRemedyPriceRangeItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="remedy-price-range-item-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'prange_id') ?>

    <?= $form->field($model, 'vial_id') ?>

    <?= $form->field($model, 'price') ?>

    <?= $form->field($model, 'start_date') ?>

    <?= $form->field($model, 'expire_date') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
