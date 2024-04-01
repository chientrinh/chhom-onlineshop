<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product-cost/_form.php $
 * $Id: _form.php 2307 2016-03-26 08:33:43Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\factory\ProductCost
 * @var $form yii\widgets\ActiveForm
 */

if($model->isNewRecord)
    $disabled = [];
else
    $disabled = ['disabled' => 'disabled'];
?>

<div class="product-cost-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ean13')->textInput($disabled) ?>

    <?= $form->field($model, 'cost')->textInput() ?>

    <?= $form->field($model, 'start_date')
         ->widget(\yii\jui\DatePicker::className(),
                  [
                        'language' => Yii::$app->language,
                        'clientOptions' =>[
                        'dateFormat'    => 'yy-m-d',
                        'language'      => Yii::$app->language,
                        'country'       => 'JP',
                        'showAnim'      => 'fold',
                        'yearRange'     => 'c-10:c+10',
                        'changeMonth'   => true,
                        'changeYear'    => true,
                        'autoSize'      => true,
                        'showOn'        => "button",
                        'htmlOptions'=>[
                            'style'=>'width:80px;',
                            'font-weight'=>'x-small',
                            'class' => 'form-group',
                        ],]]) ?>

    <?= $form->field($model, 'end_date')
         ->widget(\yii\jui\DatePicker::className(),
                  [
                        'language' => Yii::$app->language,
                        'clientOptions' =>[
                        'dateFormat'    => 'yy-m-d',
                        'language'      => Yii::$app->language,
                        'country'       => 'JP',
                        'showAnim'      => 'fold',
                        'yearRange'     => 'c-10:c+10',
                        'changeMonth'   => true,
                        'changeYear'    => true,
                        'autoSize'      => true,
                        'showOn'        => "button",
                        'htmlOptions'=>[
                            'style'=>'width:80px;',
                            'font-weight'=>'x-small',
                            'class' => 'form-group',
                        ],]]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '追加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
