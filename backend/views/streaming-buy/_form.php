<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/streaming-buy/_form.php $
 * $Id: _form.php 2286 2016-03-21 06:11:00Z mori $
 *
 * @var $this yii\web\View
 * @var $form yii\widgets\ActiveForm
 * @var $model common\models\StreamingBuy
 */

$lives = \common\models\Streaming::find()->all();
$lives = \yii\helpers\ArrayHelper::map($lives, 'streaming_id', 'name');
?>

<div class="streaming-buy-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'customer_id')->textInput() ?>

    <?= $form->field($model, 'streaming_id')->dropDownList($lives) ?>


    <?= $form->field($model, 'expire_date', ['inputOptions'=>['class'=>'form-control  col-md-12']])->textInput() ?>



    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '追加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

