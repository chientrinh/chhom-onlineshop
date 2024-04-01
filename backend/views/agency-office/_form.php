<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/agency-office/_form.php $
 * $Id: _form.php 3849 2018-04-18 05:04:13Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\AgencyOffice
 * @var $form yii\widgets\ActiveForm
 */

$prefs = \yii\helpers\ArrayHelper::map(\common\models\Pref::find()->all(), 'pref_id', 'name');
?>

<div class="agency-office-form">

    <?php $form = \yii\bootstrap\ActiveForm::begin([
    'fieldConfig' => [
        'template' => "{input}\n{hint}\n{error}",
    ],
    'enableClientValidation' => false,
    ]); ?>

    <?= \yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'attribute' => 'customer.name',
            'format'    => 'html',
            'value'     => Html::a($model->customer->name, ['/customer/view','id'=>$model->customer_id]),
        ],
        [
            'attribute' => 'company_name',
            'format'    => 'raw',
            'value'     => Html::tag('div', $form->field($model, 'company_name')->textInput(['maxlength' => 255]))
        ],
        [
            'attribute' => 'person_name',
            'format'=> 'raw',
            'value' => $form->field($model, 'person_name')->textInput(['maxlength' => 255])
        ],
        [
            'attribute' => 'tel',
            'format'=> 'raw',
            'value' => Html::activeTextInput($model,'tel01') . ' &minus; '
                     . Html::activeTextInput($model,'tel02') . ' &minus; '
                     . Html::activeTextInput($model,'tel03')
        ],
        [
            'attribute' => 'addr',
            'format'=> 'raw',
            'value' => Html::activeTextInput($model,'zip01') . ' &minus; '
                     . Html::activeTextInput($model,'zip02')  . ' &nbsp; '
                     . Html::submitButton('住所検索',['name'=>'scenario','value'=>'zip2addr','class'=>'btn btn-primary'])
                     . '<br>'
                     . Html::activeDropDownList($model,'pref_id',$prefs)
                     . (is_array($model->addr01)
                         ? Html::activeDropDownList($model,'addr01',array_combine($model->addr01,$model->addr01))
                             : $form->field($model,'addr01'))
                     . $form->field($model,'addr02')
        ],
        [
            'attribute' => 'fax',
            'format'=> 'raw',
            'value' => Html::activeTextInput($model,'fax01') . ' &minus; '
                     . Html::activeTextInput($model,'fax02') . ' &minus; '
                     . Html::activeTextInput($model,'fax03')
        ],
        [
            'attribute' => 'payment_date',
            'format'=> 'raw',
            'value' => $form->field($model, 'payment_date')->dropDownList($model->getPaymentDays(), ['class' => 'input-sm']),
            // 'value' => Html::activeDropDownList($model,'payment_date', $model->getPaymentDays()) . ' 日 '
        ],
    ],
    ]) ?>
    <?php
      // 顧客管理画面からの遷移であることをgetパラメータから確認し、存在すればhiddenパラメータにセットする
       $request = Yii::$app->request;
      if($request->get("from_customer")) {
          echo  $form->field($model, 'from_customer')->hiddenInput(['value'=>$request->get("from_customer")])->label(false);
      }
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '作成' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php $form->end() ?>

    <?php if($model->hasErrors()): ?>
        <?= Html::errorSummary($model,['class'=>'alert alert-danger']) ?>
    <?php endif ?>

</div>
