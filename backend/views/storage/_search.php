<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/storage/_search.php $
 * $Id: _search.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchStorage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="storage-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'storage_id') ?>

    <?= $form->field($model, 'src_id') ?>

    <?= $form->field($model, 'dst_id') ?>

    <?= $form->field($model, 'staff_id') ?>

    <?= $form->field($model, 'ship_date') ?>

    <?php // echo $form->field($model, 'pick_date') ?>

    <?php // echo $form->field($model, 'create_date') ?>

    <?php // echo $form->field($model, 'update_date') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
