<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedysku/_search.php $
 * $Id: _search.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchRemedySku */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="remedy-sku-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'sku_id') ?>

    <?= $form->field($model, 'type_id') ?>

    <?= $form->field($model, 'vial_id') ?>

    <?= $form->field($model, 'price') ?>

    <?= $form->field($model, 'start_date') ?>

    <?php // echo $form->field($model, 'expire_date') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
