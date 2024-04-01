<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/streaming-buy/_search.php $
 * $Id: _search.php 804 2020-04-28 12:31:58Z kawai $
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\StreamingBuySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="streaming-buy-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'streaming_buy_id') ?>

    <?= $form->field($model, 'streaming_id') ?>

    <?= $form->field($model, 'customer_id') ?>


    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
