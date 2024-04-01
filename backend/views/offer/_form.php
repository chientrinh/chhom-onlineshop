<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/offer/_form.php $
 * $Id: _form.php 3290 2017-05-14 09:22:48Z naito $
 *
 * @var $this yii\web\View
 * @var $model common\models\Offer
 * @var $form yii\widgets\ActiveForm
 */

$categories = ArrayHelper::map(\common\models\Category::find()->all(), 'category_id', function($model){ return strtoupper($model->seller->key) .':'. $model->name; });
$grades     = ArrayHelper::map(\common\models\CustomerGrade::find()->all(), 'grade_id', 'name');
?>

<div class="offer-form">

    <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'category_id')->dropDownLIst($categories) ?>

    <?= $form->field($model, 'grade_id')->dropDownList($grades) ?>

    <?= $form->field($model, 'discount_rate')->textInput() ?>

    <?= $form->field($model, 'point_rate')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
