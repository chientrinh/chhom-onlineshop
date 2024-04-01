<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/zip/_search.php $
 * $Id: _search.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ZipSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="zip-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'region') ?>

    <?= $form->field($model, 'zipcode') ?>

    <?= $form->field($model, 'pref_id') ?>

    <?= $form->field($model, 'city') ?>

    <?= $form->field($model, 'town') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
