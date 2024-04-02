<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\sodan\Homoeopath;

$title = '休業日';
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['holiday-setting']];
$labels = ArrayHelper::getColumn($this->params['breadcrumbs'], 'label');
rsort($labels);
$this->title = implode(' | ', $labels) . ' | '. Yii::$app->name;

$query = ($branch_id) ? Homoeopath::find()->active()->multibranch($branch_id) : Homoeopath::find()->active();
$hpath = ArrayHelper::merge(['' => ''], ArrayHelper::map($query->all(),'homoeopath_id','customer.homoeopathname'));
asort($hpath);

if ($hpath_id)
    $model->homoeopath_id = $hpath_id;

$jscode = "
// フィルタ設定
var homoeopathArray = new Array();

$('select[name=\'Holiday[homoeopath_id]\']').children().each(function(){
   homoeopathArray.push( { value:$(this).val(), body:$(this).html() });
});

$('#homoeopath-filter').keyup(function(){
    option_filter($(this).val(), homoeopathArray, 'homoeopath_id');
});

";
$this->registerJs($jscode);

echo <<<EOF
<script>
function option_filter (s, options, attr) {
    $('select[name=\'Holiday[' + attr + ']\']').empty();
    if (s == ''){
       $(options).each(function(i, o){
          $('select[name=\'Holiday[' + attr + ']\']').append( $('<option>').val(o.value).text(o.body));
       });
    } else {
       $('select[name=\'Holiday[' + attr + ']\']').append( $('<option>').val('').text(''));
       options.filter(function(o, i){
          if (o.body.toLowerCase().indexOf(s.toLowerCase()) != -1){
             $('select[name=\'Holiday[' + attr + ']\']').append( $('<option>').val(o.value).text(o.body));
          }
       });
    }
}
</script>
EOF;
?>
<div class="holiday-form">

    <?php $form = \yii\bootstrap\ActiveForm::begin([
        'enableClientScript' => false,
        'fieldConfig' => [
        ],
    ]); ?>

    <?= Html::errorSummary($model,['class'=>'alert alert-danger']) ?>
    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <?= $form->field($model, 'all_day')->checkbox() ?>
                <?= $form->field($model, 'holiday_flg')->checkbox() ?>
            </div>
            <div class="row">
                <!-- hpath_id -->
                <div class="col-md-5">
                    <?= Html::tag('label',$model->getAttributeLabel('homoeopath_id')) ?>
                </div>
                <div class="col-md-5">
                    <?= Html::textInput('homoeopath-filter', '', [
                        'class' => 'form-control',
                        'id' => 'homoeopath-filter',
                        'autocomplete' => 'off',
                        'style' => 'margin:5px 50%;'
                    ]) ?>
                </div>
                <?= $form->field($model, 'homoeopath_id')->dropDownList($hpath)->label(false) ?>
            </div>
            <div class="row">
                <div class="row col-md-12">
                    <?= $form->field($model, 'title')->textInput() ?>
                </div>
                <div class="row">
                    <div class="col-md-4">
                    <?= $form->field($model, 'date')
                     ->widget(\yii\jui\DatePicker::className(),
                        [
                            'language' => Yii::$app->language,
                            'clientOptions' =>[
                                'dateFormat'    => 'yy-m-d',
                                'language'      => Yii::$app->language,
                                'country'       => 'JP',
                                'showAnim'      => 'fold',
                                'yearRange'     => 'c-5:c+5',
                                'changeMonth'   => true,
                                'changeYear'    => true,
                                'autoSize'      => true,
                                'showOn'        => "button",
                                'htmlOptions'=>[
                                    'style'=>'width:80px;',
                                    'font-weight'=>'x-small',
                                ],],
                            'options' => ['class' => 'form-control'],
                        ]) ?>
                    </div>
                    <div class="col-md-4">
                    <?= $form->field($model, 'start_time')
                        ->widget(\kartik\time\TimePicker::className(),[
                            'pluginOptions' => [
                                'defaultTime'  => '09:30',
                                'showMeridian' => false,
                                'showSeconds'  => false,
                                'minuteStep'   => 5,
                            ]
                        ]) ?>
                    </div>
                    <div class="col-md-4">
                    <?= $form->field($model, 'end_time')
                        ->widget(\kartik\time\TimePicker::className(),[
                            'pluginOptions' => [
                                'defaultTime'  => '17:00',
                                'showMeridian' => false,
                                'showSeconds'  => false,
                                'minuteStep'   => 5,
                            ]
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row col-md-12 col-xs-8">
            <?= $form->field($model, 'note')->textArea() ?>
        </div>
        <div class="col-md-12 col-xs-8">
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? '追加' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
    <?php $form->end(); ?>
</div>
