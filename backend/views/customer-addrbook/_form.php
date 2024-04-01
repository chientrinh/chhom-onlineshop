<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/customer-addrbook/_form.php $
 * $Id: _form.php 2995 2016-10-20 05:31:57Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\CustomerAddrbook
 * @var $form yii\widgets\ActiveForm
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;


$prefs = \common\models\Pref::find()->asArray()->all();
$prefs = \yii\helpers\ArrayHelper::map($prefs, 'pref_id','name');
$prefs[0] = '';
ksort($prefs);

?>

<div class="customer-addrbook-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'default',
        'fieldConfig' => [
        'template' => "{label}\n{input}\n{hint}\n{error}",
        'enableLabel' => false,
        'enableError' => true,
        'enableClientValidation' => false,
    ],
    ]); ?>

    <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'customer_id',
                'format'    => 'html',
                'value'     => ($c = $model->customer) ? Html::a($c->name,['/customer/view','id'=>$model->customer_id]) : null,
            ],
            [
                'attribute' => 'zip',
                'format'    => 'raw',
                'value'     => '<div class="row"><div class="col-md-4">'
                             . $form->field($model, 'zip01')->textInput(['maxlength' => 255])
                             . '</div><div class="col-md-4">'
                             . $form->field($model, 'zip02')->textInput(['maxlength' => 255])
                             . '</div>'
                             . Html::submitButton('住所を検索',['name'=>'scenario','value'=>'zip2addr','class'=>'btn btn-primary'])
                             . '</div>',
            ],
            [
                'attribute' => 'addr',
                'format'    => 'raw',
                'value'     => $form->field($model, 'pref_id')->dropDownList($prefs,['class'=>'form-control'])
                           . '<br>'
                           . $form->field($model, 'addr01')->textInput(['maxlength' => 255,'inline'=>false])
                           . $form->field($model, 'addr02')->textInput(['maxlength' => 255])
            ],
            [
                'attribute' => 'name',
                'format'    => 'raw',
                'value'     => '<div class="row"><div class="col-md-4">'
                             . $form->field($model, 'name01')->textInput(['maxlength' => 255])
                             . '</div><div class="col-md-4">'
                             . $form->field($model, 'name02')->textInput(['maxlength' => 255])
                             . '</div>',
            ],
            [
                'attribute' => 'kana',
                'format'    => 'raw',
                'value'     => '<div class="row"><div class="col-md-4">'
                             . $form->field($model, 'kana01')->textInput(['maxlength' => 255])
                             . '</div><div class="col-md-4">'
                             . $form->field($model, 'kana02')->textInput(['maxlength' => 255])
                             . '</div>',
            ],
            [
                'attribute' => 'tel',
                'format'    => 'raw',
                'value'     => '<div class="row"><div class="col-md-4">'
                             . $form->field($model, 'tel01')->textInput(['maxlength' => 5])
                             . '</div><div class="col-md-4">'
                             . $form->field($model, 'tel02')->textInput(['maxlength' => 5])
                             . '</div><div class="col-md-4">'
                             . $form->field($model, 'tel03')->textInput(['maxlength' => 5])
                             . '</div>',
            ],
        ],
    ]) ?>
    <?= $form->errorSummary($model) ?>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
