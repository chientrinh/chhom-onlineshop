<?php

use yii\helpers\Html;
use common\models\sodan\Homoeopath;
use \yii\helpers\ArrayHelper;
use common\models\sodan\Client;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/wait-list/_form.php $
 * $Id: _form.php 2518 2016-05-18 04:10:44Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\sodan\BookTemplate
 */
?>

<div class="wait-list-form">

    <?php $form = \yii\bootstrap\ActiveForm::begin(); ?>

    <?= $form->field($model, 'body')->textArea(['maxlength' => true, 'rows' => 5]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '追加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php $form->end(); ?>

    <?php if($model->hasErrors()): ?>
        <?= Html::errorSummary($model,['class'=>'alert alert-danger']) ?>
    <?php endif ?>

</div>
