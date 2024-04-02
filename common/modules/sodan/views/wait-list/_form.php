<?php

use yii\helpers\Html;
use common\models\sodan\Homoeopath;
use \yii\helpers\ArrayHelper;
use common\models\sodan\Client;
use yii\helpers\Url;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/wait-list/_form.php $
 * $Id: _form.php 3851 2018-04-24 09:07:27Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\sodan\WaitList
 */
$ajaxUrl = Url::to(['fetch-homoeopath']);
$jscode = "
$('select[name=\'WaitList[branch_id]\']').change(function(){
  var branch_id = $(this).val();
  $.ajax({
    type: 'POST',
    url: '{$ajaxUrl}',
    data: {
      branch_id : branch_id
    }
  }).done(function(result) {
    var homoeopaths = JSON.parse(result);

    // 相談種別リストを作成し直す
    $('select[name=\'WaitList[homoeopath_id]\']').empty();
    $('select[name=\'WaitList[homoeopath_id]\']').append($('<option>').text('').val(''));
    for (var i in homoeopaths) {
      if (!homoeopaths[i]) {
        continue;
      }
      plist = $('<option>').text(homoeopaths[i]).val(i);
      $('select[name=\'WaitList[homoeopath_id]\']').append(plist);
    }
  }).fail(function(result) {
    alert('データ取得に失敗しました');
  });
});
";
$this->registerJs($jscode);

$branch = \common\models\Branch::find()->center()->all();
$branch = ArrayHelper::merge([''], ArrayHelper::map($branch,'branch_id','name'));

$query = Homoeopath::find()->with('customer')->active();
if ($staff_branch) {
    $query->multibranch($staff_branch);
}
$hpath = ArrayHelper::merge([''], ArrayHelper::map($query->all(), 'homoeopath_id','customer.homoeopathname'));
ksort($hpath);

$query = ($staff_branch) ? Client::find()->active()->where(['branch_id' => $staff_branch]) : Client::find()->active();
$client = ArrayHelper::merge([''], ArrayHelper::map($query->all(),'client_id','name'));
?>

<div class="wait-list-form">

    <?php $form = \yii\bootstrap\ActiveForm::begin(); ?>

    <?= $form->field($model, 'client_id')->dropDownList($client) ?>

    <?= $form->field($model, 'branch_id')->dropDownList($branch) ?>

    <?= $form->field($model, 'homoeopath_id')->dropDownList($hpath) ?>

    <?= $form->field($model, 'expire_date')
         ->widget(\yii\jui\DatePicker::className(),
                  [
                      'language' => Yii::$app->language,
                      'clientOptions' =>[
                          'dateFormat'    => 'yy-m-d',
                          'language'      => Yii::$app->language,
                          'country'       => 'JP',
                          'showAnim'      => 'fold',
                          'yearRange'     => 'c-1:c+1',
                          'changeMonth'   => true,
                          'changeYear'    => true,
                          'autoSize'      => true,
                          'showOn'        => "button",
                          'htmlOptions'=>[
                              'style'=>'width:80px;',
                              'font-weight'=>'x-small',
                              'class' => 'form-control',
                          ],],
                      //'options' => ['class' => 'form-control'],
                  ]) ?>

    <?= $form->field($model, 'note')->textArea(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '追加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php $form->end(); ?>

    <?php if($model->hasErrors()): ?>
        <?= Html::errorSummary($model,['class'=>'alert alert-danger']) ?>
    <?php endif ?>

</div>
