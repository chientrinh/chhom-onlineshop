<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/information/_form.php $
 * $Id: _form.php 3450 2017-06-27 08:51:15Z kawai $
 */
/* @var $this yii\web\View */
/* @var $model common\models\Information */
/* @var $form yii\widgets\ActiveForm */

$companies = \yii\helpers\ArrayHelper::map(\common\models\Company::find()->all(), 'company_id', 'name');

?>

<div class="information-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'company_id')->dropDownList($companies) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textArea(['maxlength' => true, 'rows' => 19]) ?>

    <?= $form->field($model, 'pub_date')->widget(DatePicker::className(),
                    [
                        'language' => Yii::$app->language,
                        'clientOptions' =>[
                        'dateFormat'    => 'd-m-yy',
                        'language'      => Yii::$app->language,
                        'country'       => 'JP',
                        'showAnim'      => 'fold',
                        'yearRange'     => 'c-25:c+25',
                        'changeMonth'   => true,
                        'changeYear'    => true,
                        'autoSize'      => true,
                        'showOn'        => "button",
                        //'buttonImage'=> "images/calendar.gif",
                        'htmlOptions'=>[
                            'style'=>'width:80px;',
                            'font-weight'=>'x-small',
                        ],]]) ?>

    <?= $form->field($model, 'expire_date')->widget(DatePicker::className(),
                    [
                        'language' => Yii::$app->language,
                        'clientOptions' =>[
                        'dateFormat'    => 'd-m-yy',
                        'language'      => Yii::$app->language,
                        'country'       => 'JP',
                        'showAnim'      => 'fold',
                        'yearRange'     => 'c-25:c+25',
                        'changeMonth'   => true,
                        'changeYear'    => true,
                        'autoSize'      => true,
                        'showOn'        => "button",
                        //'buttonImage'=> "images/calendar.gif",
                        'htmlOptions'=>[
                            'style'=>'width:80px;',
                            'font-weight'=>'x-small',
                        ],]]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
