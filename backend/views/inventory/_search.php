<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/inventory/_search.php $
 * $Id: _search.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchInventory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="inventory-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'inventory_id') ?>

    <?= $form->field($model, 'branch_id') ?>

    <?= $form->field($model, 'staff_id') ?>

    <?= $form->field($model, 'create_date') ?>

    <?= $form->field($model, 'update_date') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
