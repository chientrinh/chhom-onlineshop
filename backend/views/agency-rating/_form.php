<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/agency-rating/_form.php $
 * $Id: _form.php 3106 2016-11-25 01:54:47Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\AgencyRating
 */

$companies = \yii\helpers\ArrayHelper::map(\common\models\Company::find()->where(['company_id'=>[2,3,4]])->all(), 'company_id', 'key');

?>

<div class="agency-rating-form">

    <h1>
        <?= ArrayHelper::getValue($model, 'customer.name') ?>
        <small>さんの割引率</small>
    </h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'company_id')->dropDownList($companies) ?>

    <?= $form->field($model, 'discount_rate')->textInput() ?>

    <?= $form->field($model, 'start_date')->textInput([
        'filter' => \yii\jui\DatePicker::widget([
            'model'      => $model,
            'attribute'  => 'start_date',
            'language'   => Yii::$app->language,
            'dateFormat' => 'yyyy-MM-dd',
            'options'    => ['class'=>'form-control col-md-12'],
            'clientOptions' => [
                'country'     => 'JP',
                'yearRange'   => 'c-1:c+1',
                'changeYear'  => true,
                'changeMonth' => true,
            ],
        ])
    ]) ?>

    <?= $form->field($model, 'end_date')->textInput([
        'filter' => \yii\jui\DatePicker::widget([
            'model'      => $model,
            'attribute'  =>'end_date',
            'language'   => Yii::$app->language,
            'dateFormat' => 'yyyy-MM-dd',
            'options'    => ['class'=>'form-control col-md-12'],
            'clientOptions' => [
                'country'     => 'JP',
                'yearRange'   => 'c-1:c+1',
                'changeYear'  => true,
                'changeMonth' => true,
            ],
        ])
    ]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '作成' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?= $form->errorSummary($model) ?>

    <?php $form->end(); ?>

</div>
