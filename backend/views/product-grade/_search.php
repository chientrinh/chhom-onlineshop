<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product-grade/_search.php $
 * $Id: _search.php 804 2020-04-28 12:31:58Z kawai $
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProductGradeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-grade-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'product_id') ?>

    <?= $form->field($model, 'grade_id') ?>

    <?= $form->field($model, 'price') ?>

    <?= $form->field($model, 'tax') ?>



    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
