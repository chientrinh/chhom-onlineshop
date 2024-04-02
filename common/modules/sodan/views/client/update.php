<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use \yii\helpers\ArrayHelper;
use common\models\sodan\Homoeopath;
use yii\helpers\Url;
use common\models\FileForm;
use yii\bootstrap\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/client/update.php $
 * $Id: update.php 4060 2018-11-16 05:59:04Z kawai $
 *
 * @var $this yii\web\View
 * @var $model common\models\sodan\Interview
 */

$this->title = sprintf('%s | %s | %s', $model->name, '相談会', Yii::$app->name);
$this->params['breadcrumbs'][] = ['label'=>$model->name,'url'=>['view','id'=>$model->client_id]];

$branch = \common\models\Branch::find()->center()->all();
$branch = ArrayHelper::merge([''], ArrayHelper::map($branch,'branch_id','name'));

$branch_id = $model->branch_id;
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
<div class="interview-view">    

    <div class="col-md-12">
        <div class="panel panel-default col-md-8">
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'action' => ['update','id'=>$model->client_id, 'target'=>'client','attribute'=>'skype'],
                    'layout' => 'default',
                    'method' => 'post',
                ]); ?>
                <h2>
                    <?= $model->name ?>
                </h2>
                <?= DetailView::widget([
                    'model' => $model,
                    'options'    => ['class'=>'table table-condensed'],
                    'attributes' => [
                        [
                            'label'    => '',
                            'format'   => 'html',
                            'value'    => (($birth = $model->customer->birth)
                                ? Yii::$app->formatter->asDate($birth, sprintf('php: Y-m-d 生まれ  %02d 才',$model->customer->age))
                                    : Html::tag('span','(生年月日は不明)',['class'=>'not-set'])
                            ) . '&nbsp;' . $model->kana . Html::a('詳細',['/customer/view','id'=>$model->client_id],['class'=>'btn btn-xs btn-default pull-right'])
                        ],
                        [
                            'attribute'=> 'animal_flg',
                            'format'   => 'raw',
                            'value'    => Html::radioList('Client[animal_flg]', $model->animal_flg, ['人間', '動物'])
                        ],
                        [
                            'attribute'=> 'branch_id',
                            'format'   => 'raw',
                            'value'    => $form->field($model,'branch_id')->label(false)->dropDownList($branch)
                        ],
                        [
                            'attribute'=> 'homoeopath_id',
                            'format'   => 'raw',
                            'value'    => $form->field($model,'homoeopath_id')->label(false)->dropDownList($hpaths)
                        ],
                        [
                            'attribute'=> 'parent_name',
                            'format'   => 'raw',
                            'value'    => $form->field($model, 'parent_name')->label(false)->textInput()
                        ],
                        [
                            'attribute'=> 'ng_flg',
                            'format'   => 'raw',
                            'value'    => Html::radioList('Client[ng_flg]', $model->ng_flg, [1 => 'NG', 0 => 'OK'])
                        ],
                        [
                            'attribute'=> 'note',
                            'format'   => 'raw',
                            'value'    => $form->field($model,'note')->label(false)->textArea()
                        ],
                        [
                            'attribute'=> 'skype',
                            'format'   => 'raw',
                            'value'    => $form->field($model,'skype')->label(false)->textInput()
                        ],
                    ]
                ])?>
                <?= Html::submitButton('更新',['class'=>'btn btn-primary']) ?>
                <?php $form->end() ?>
            </div>
        </div>
        <div class="col-md-4">
            <?php $ff = new FileForm(); ?>
            <div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?= Html::label($model->getAttributeLabel('photo')) ?>
                    </div>
                    <div class="panel-body">
                        <?php $form = ActiveForm::begin([
                            'id'     => 'form-photo',
                            'action' => ['fileupload','id' => 'photo', 'client_id' => $model->client_id],
                            'layout' => 'default',
                            'method' => 'post',
                            'options'=> ['enctype' => 'multipart/form-data'],
                        ]); ?>
                        <?= $form->field($ff, 'tgtFile')->label(false)->fileInput() ?>
                        <?= Html::submitButton('登録' ,['class'=>'btn btn-primary']) ?>
                        <?php $form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

