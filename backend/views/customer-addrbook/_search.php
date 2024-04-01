<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/customer-addrbook/_search.php $
 * $Id: _search.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SearchCustomerAddrbook */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-addrbook-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'customer_id') ?>

    <?= $form->field($model, 'name01') ?>

    <?= $form->field($model, 'name02') ?>

    <?= $form->field($model, 'kana01') ?>

    <?php // echo $form->field($model, 'kana02') ?>

    <?php // echo $form->field($model, 'zip01') ?>

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
