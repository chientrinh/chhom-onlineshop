<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/company/_search.php $
 * $Id: _search.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchCompany */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'company_id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'manager') ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'zip01') ?>

    <?php // echo $form->field($model, 'zip02') ?>

    <?php // echo $form->field($model, 'pref_id') ?>

    <?php // echo $form->field($model, 'addr01') ?>

    <?php // echo $form->field($model, 'addr02') ?>

    <?php // echo $form->field($model, 'tel01') ?>

    <?php // echo $form->field($model, 'tel02') ?>

    <?php // echo $form->field($model, 'tel03') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
