<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\EventVenue */
/* @var $form yii\widgets\ActiveForm */

$branch = \common\models\Branch::find()->center()->all();
$branch = ArrayHelper::map($branch,'branch_id','name');

?>
<div class="event-venue-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'product_id')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'branch_id')->dropDownList($branch) ?>

   <?= $form->field($model, 'event_date')->widget(\yii\jui\DatePicker::className(),
         [
                      'language' => Yii::$app->language,
                      'clientOptions' =>[
                          'dateFormat'    => 'yy-m-d',
                          'language'      => Yii::$app->language,
                          'country'       => 'JP',
                          'showAnim'      => 'fold',
                          'yearRange'     => 'c-5:c+5',
                          'changeMonth'   => true,
                          'changeYear'    => true,
                          'autoSize'      => true,
                          'showOn'        => "button",
                          'htmlOptions'=>[
                              'style'=>'width:80px;',
                              'font-weight'=>'x-small',
                          ],],
                      'options' => ['class' => 'form-control'],
        ]) ?>

    <?= $form->field($model, 'start_time')->widget(\kartik\time\TimePicker::className(),
    	[
             'pluginOptions' => [
                 'defaultTime'  => '10:00',
                 'showMeridian' => false,
                 'showSeconds'  => false,
                 'minuteStep'   => 5,
             ]
         ]) ?>

    <?= $form->field($model, 'end_time')->widget(\kartik\time\TimePicker::className(),
    	[
             'pluginOptions' => [
                 'defaultTime'  => '12:00',
                 'showMeridian' => false,
                 'showSeconds'  => false,
                 'minuteStep'   => 5,
             ]
         ]) ?>

    <?= $form->field($model, 'pub_date')->textInput() ?>

    <?= $form->field($model, 'capacity')->textInput() ?>

    <?= $form->field($model, 'allow_child')->dropDownList([0=>'いいえ',1=>'はい']) ?>

    <?= $form->field($model, 'overbook')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
