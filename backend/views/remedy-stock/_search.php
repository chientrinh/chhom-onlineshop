<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy-stock/_search.php $
 * $Id: _search.php 987 2015-05-03 09:03:07Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\SearchRemedyStock * @var $form yii\widgets\ActiveForm
 */
?>

<div class="remedy-stock-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'remedy_id') ?>

    <?= $form->field($model, 'potency_id') ?>

    <?= $form->field($model, 'prange_id') ?>

    <?= $form->field($model, 'vial_id') ?>

    <?= $form->field($model, 'in_stock') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
