<?php

use yii\helpers\Html;

/**
 * $URL: http://test-webhj.homoeopathy.co.jp:8000/svn/MALL/backend/views/information/_form.php $
 * $Id: _form.php 1165 2015-07-18 08:05:15Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\CustomerMembership
 */

$dropDown = \yii\helpers\ArrayHelper::map(\common\models\Membership::find()->all(), 'membership_id','name');

?>

<div class="customer-membership-form">

    <?php $form = \yii\bootstrap\ActiveForm::begin(); ?>

    <?php if($model->validate(['customer_id'])): ?>
    <?= $form->field($model->customer, 'name')->textInput(['disabled'=>'disabled']) ?>
    <?php endif ?>

    <?= $form->field($model, 'membership_id')->dropDownList($dropDown) ?>

    <?= $form->field($model, 'start_date')->widget(\yii\jui\DatePicker::className(),[
        'language' => Yii::$app->language,
        'clientOptions' =>[
            'dateFormat'    => 'yy-mm-dd',
            'language'      => Yii::$app->language,
            'country'       => 'JP',
            'showAnim'      => 'fold',
            'yearRange'     => 'c-25:c+25',
            'changeMonth'   => true,
            'changeYear'    => true,
            'autoSize'      => true,
            'showOn'        => "button",
        ],
        'options'=>[
        ],
    ]) ?>

    <?= $form->field($model, 'expire_date')->widget(\yii\jui\DatePicker::className(),[
        'language' => Yii::$app->language,
        'clientOptions' =>[
            'dateFormat'    => 'yy-mm-dd',
            'language'      => Yii::$app->language,
            'country'       => 'JP',
            'showAnim'      => 'fold',
            'yearRange'     => 'c-100:c+100',
            'changeMonth'   => true,
            'changeYear'    => true,
            'autoSize'      => true,
            'showOn'        => "button",
        ],
        'options'=>[
        ],
    ]) ?>

    <?= $form->field($model, 'note')->textArea(['placeholder'=>'所属を追加／修正する理由を入力します']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '追加' : '更新', ['id'=> 'submit-button', 'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php $form->end(); ?>

    <?= Html::errorSummary($model, ['class'=>'alert alert-danger']) ?>
</div>
