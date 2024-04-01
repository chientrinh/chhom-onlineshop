<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/zip/_form.php $
 * $Id: _form.php 2667 2016-07-07 08:26:14Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Zip
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

$pref = \common\models\Pref::find()->all();
$pref = ArrayHelper::map($pref, 'pref_id','name');
?>

<div class="zip-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'region')->textInput() ?>

    <?= $form->field($model, 'zipcode')->textInput() ?>

    <?= $form->field($model, 'pref_id')->dropDownList($pref) ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'town')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'yamato_22')->dropDownList([10=>10,15=>15,20=>20,30=>30]) ?>

    <?= $form->field($model, 'sagawa_22')->dropDownList([10=>10,15=>15,20=>20,30=>30]) ?>

    <?= $form->field($model, 'spat')->dropDownList([0=>'不可',1=>'可']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '追加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
