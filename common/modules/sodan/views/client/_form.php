<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\Branch;
use common\models\sodan\Homoeopath;
use yii\helpers\Url;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/client/_form.php $
 * $Id: _form.php 2518 2016-05-18 04:10:44Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\sodan\WaitList
 */

$query = Branch::find()->center();
$branch = ArrayHelper::merge(['' => ''], ArrayHelper::map($query->all(),'branch_id','name'));

$query = ($branch_id) ? Homoeopath::find()->active()->multibranch($branch_id) : Homoeopath::find()->active();
$hpaths = ArrayHelper::merge(['' => ''], ArrayHelper::map($query->all(),'homoeopath_id','customer.homoeopathname'));

$ajaxUrl = Url::to(['fetch-homoeopath']);
$jscode = "
$('select[name=\'Client[branch_id]\']').change(function(){
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
    $('select[name=\'Client[homoeopath_id]\']').empty();
    $('select[name=\'Client[homoeopath_id]\']').append($('<option>').text('').val(''));
    for (var i in homoeopaths) {
      if (!homoeopaths[i]) {
        continue;
      }
      plist = $('<option>').text(homoeopaths[i]).val(i);
      $('select[name=\'Client[homoeopath_id]\']').append(plist);
    }
  }).fail(function(result) {
    alert('データ取得に失敗しました');
  });
});
";
$this->registerJs($jscode);
?>

<div class="wait-list-form">

<div class="form-group field-waitlist-client_id">
<label class="control-label" for="waitlist-client_id">クライアント</label>
</div>

    <div class="col-md-3">
        <?php if($model->client): ?>
            <?= Html::a($model->client->name, ['/customer/view', 'id' => $model->client_id]) ?>
        <?php else: ?>
            <p class="text-muted">(未指定)</p>
        <?php endif ?>
    </div>

    <div class="col-md-9">
    <?= $this->render('search-client',[
        'keyword'  => Yii::$app->request->post('keyword'),
    ]) ?>
    </div>

    <?php $form = \yii\bootstrap\ActiveForm::begin(); ?>

    <span><?= Html::label($model->getAttributeLabel('animal_flg')) ?></span>
    <?= Html::radioList('Client[animal_flg]', '0', ['人間', '動物']) ?>

    <?= $form->field($model, 'branch_id')->dropDownList($branch) ?>

    <?= $form->field($model, 'homoeopath_id')->dropDownList($hpaths) ?>

    <?= $form->field($model, 'parent_name')->textInput(['maxlength' => 256]) ?>

    <span><?= Html::label($model->getAttributeLabel('ng_flg')) ?></span>
    <?= Html::radioList('Client[ng_flg]', '1', [1 =>'NG', 0 => 'OK']) ?>

    <?= $form->field($model, 'skype')->textInput(['maxlength' => 256]) ?>

    <?= $form->field($model, 'note')->textArea(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '追加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php $form->end(); ?>

    <?php if($model->hasErrors()): ?>
        <?= Html::errorSummary($model,['class'=>'alert alert-danger']) ?>
    <?php endif ?>

</div>
