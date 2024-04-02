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

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'kana')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'price')->textInput(['class'=>'form-control js-zenkaku-to-hankaku']) ?>

    <?= $form->field($model, 'summary')->textArea(['rows'=> 3, 'maxlength' => 255]) ?>

    <?= $form->field($model, 'description')->textArea(['rows' => 5]) ?>

    <?= $form->field($model, 'start_date')->textInput([
                'filter' => \yii\jui\DatePicker::widget([
                    'model' => $model,
                    'attribute'=>'start_date',
                    'language' => 'ja',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => ['class'=>'form-control col-md-12'],
                    'clientOptions' => [
                        'country'     => 'JP',
                        'yearRange'   => 'c-1:c+1',
                        'changeYear'  => true,
                        'changeMonth' => true,
                    ],
                ])]
    ) ?>

    <?= $form->field($model, 'expire_date')->textInput([
                'filter' => \yii\jui\DatePicker::widget([
                    'model' => $model,
                    'attribute'=>'expire_date',
                    'language' => 'ja',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => ['class'=>'form-control col-md-12'],
                    'clientOptions' => [
                        'country'     => 'JP',
                        'yearRange'   => 'c-1:c+1',
                        'changeYear'  => true,
                        'changeMonth' => true,
                    ],
                ])]
    ) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '追加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php $form->end(); ?>

    <?php if($model->hasErrors()): ?>
        <?= Html::errorSummary($model,['class'=>'alert alert-danger']) ?>
    <?php endif ?>

</div>
