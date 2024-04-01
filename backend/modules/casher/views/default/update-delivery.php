<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/update-delivery.php $
 * $Id: update-delivery.php 3704 2017-10-25 10:34:12Z kawai $
 *
 * $model \common\models\PurchaseDelivery
 */

$this->params['breadcrumbs'][] = 'お届け先';

use \yii\helpers\Html;

$prefs = \common\models\Pref::find()->asArray()->all();
$prefs = \yii\helpers\ArrayHelper::map($prefs, 'pref_id','name');
$prefs[0] = '';
ksort($prefs);

$dateModel = new \common\models\DeliveryDateTimeForm([
    'company_id'=> null,
    'zip01'     => $model->zip01,
    'zip02'     => $model->zip02,
    'pref_id'   => $model->pref_id,
    'date'      => $model->expect_date,
    'time_id'   => $model->expect_time,
]);
$timeModel = new \common\models\DeliveryTime();

$drop1 = $dateModel->dateCandidates;
array_unshift($drop1, "指定なし");

// ヤマトが12時〜14時指定を廃止した。time_id 2を除外したいがarray_unshiftやarray_mergeを使用すると数値添え字が振り直しになり値との対応が狂うので注意　2017/06/15
$drop2 = \yii\helpers\ArrayHelper::map($timeModel->yamato, 'time_id', 'name');
$array = ['0' => "指定なし"];
$drop2 = $array + $drop2;
?>

<div class="dispatch-default-update">

  <div class="body-content">

  <?php $form = \yii\bootstrap\ActiveForm::begin([
        'layout' => 'default',
        'fieldConfig' => [
        'template' => "{label}\n{input}\n{hint}\n{error}",
        'enableLabel' => false,
        'enableError' => true,
        'enableClientValidation' => false,
        ],
  ]) ?>
    <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
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
                             . $form->field($model, 'name01')->textInput(['maxlength' => 5])
                             . '</div><div class="col-md-4">'
                             . $form->field($model, 'name02')->textInput(['maxlength' => 5])
                             . '</div>',
            ],
            [
                'attribute' => 'kana',
                'format'    => 'raw',
                'value'     => '<div class="row"><div class="col-md-4">'
                             . $form->field($model, 'kana01')->textInput(['maxlength' => 5])
                             . '</div><div class="col-md-4">'
                             . $form->field($model, 'kana02')->textInput(['maxlength' => 5])
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
            [
                'attribute' => 'expect_date',
                'format'    => 'raw',
                'value'     => '<div class="row"><div class="col-md-4">'
                             . $form->field($model, 'expect_date')->dropDownList($drop1)
                             . '</div><div class="col-md-4">'
                             . $form->field($model, 'expect_time')->dropDownList($drop2)
                             . '</div>',
            ],
            [
                'attribute' => 'gift',
                'format'    => 'raw',
                'value'     => $form->field($model,'gift')->radioList([1=>'非表示',0=>'表示']),
            ],
        ],
    ]) ?>

    <?= Html::submitButton('保存',['name'=>'scenario','value'=>'default','class'=>'btn btn-success']) ?>

    <?= $form->errorSummary($model)?>

    <?php $form->end() ?>

  </div>

</div>
