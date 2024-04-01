<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product-grade/_form.php $
 * $Id: _form.php 2286 2016-03-21 06:11:00Z mori $
 *
 * @var $this yii\web\View
 * @var $form yii\widgets\ActiveForm
 * @var $model common\models\ProductGrade
 */

$liveTickets = \common\models\Product::find()->where(['category_id' => \common\models\Category::LIVE])->orderBy(['product_id' => SORT_DESC])->all();
$liveTickets = \yii\helpers\ArrayHelper::map($liveTickets, 'product_id', 'name');
$grades      =  \common\models\CustomerGrade::find()->all();
$grades = \yii\helpers\ArrayHelper::map($grades, 'grade_id', 'name');
$grades = array_merge(['0' => '一般'],$grades);

?>

<div class="product-grade-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'product_id')->dropDownList($liveTickets) ?>

    <?= $form->field($model, 'grade_id')->dropDownList($grades) ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <?= $form->field($model, 'tax')->textInput() ?>

    <?= $form->field($model, 'tax_rate')->textInput() ?>

    <?= $form->field($model, 'expire_date', ['inputOptions'=>['class'=>'form-control  col-md-12']])->textInput() ?>



    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '追加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

