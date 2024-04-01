<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy/_search.php $
 * $Id: _search.php 969 2015-04-30 02:53:47Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\SearchRemedy * @var $form yii\widgets\ActiveForm
 */
?>

<div class="remedy-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'remedy_id') ?>

    <?= $form->field($model, 'abbr') ?>

    <?= $form->field($model, 'latin') ?>

    <?= $form->field($model, 'ja') ?>

    <?= $form->field($model, 'concept') ?>

    <?php // echo $form->field($model, 'advertise') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
