<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\sodan\Homoeopath;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/homoeopath/add-opentime.php $
 */
$jscode = "
// フィルタ設定
var homoeopathArray = new Array();

$('select[name=\'Open[homoeopath_id]\']').children().each(function(){
   homoeopathArray.push( { value:$(this).val(), body:$(this).html() });
});

$('#homoeopath-filter').keyup(function(){
    option_filter($(this).val(), homoeopathArray, 'homoeopath_id');
});
";
$this->registerJs($jscode);

$query = ($branch_id) ? Homoeopath::find()->active()->multibranch($branch_id) : Homoeopath::find()->active();
$hpath = ArrayHelper::merge(['' => ''], ArrayHelper::map($query->all(),'homoeopath_id','customer.homoeopathname'));
asort($hpath);

$week_d_list = [
    '0' => '日曜日',
    '1' => '月曜日',
    '2' => '火曜日',
    '3' => '水曜日',
    '4' => '木曜日',
    '5' => '金曜日',
    '6' => '土曜日'
];

$title = '公開枠設定';
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['add-opentime']];
$labels = ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
rsort($labels);
$this->title = implode(' | ', $labels) . ' | '. Yii::$app->name;

echo <<<EOF
<script>
function option_filter (s, options, attr) {
    $('select[name=\'Open[' + attr + ']\']').empty();
    if (s == ''){
       $(options).each(function(i, o){
          $('select[name=\'Open[' + attr + ']\']').append( $('<option>').val(o.value).text(o.body));
       });
    } else {
       $('select[name=\'Open[' + attr + ']\']').append( $('<option>').val('').text(''));
       options.filter(function(o, i){
          if (o.body.toLowerCase().indexOf(s.toLowerCase()) != -1){
             $('select[name=\'Open[' + attr + ']\']').append( $('<option>').val(o.value).text(o.body));
          }
       });
    }
}
</script>
EOF;
?>

<?= Html::errorSummary($model,['class'=>'alert alert-danger']) ?>
<div class="room-create">
    <p>
        ホメオパスの公開枠設定をします。設定したい曜日と時間帯を選択してください。
    </p>
    <div class="interview-form">
        <?php $form = \yii\bootstrap\ActiveForm::begin([
            'id' => 'submit-regular',
            'enableClientScript' => false,
        ]); ?>

        <div class="row col-md-12">
            <div class="col-md-6">
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
                <?= $form->field($model, 'week_day')->checkboxList($week_d_list) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'start_time')
                 ->widget(\kartik\time\TimePicker::className(),[
                     'pluginOptions' => [
                         'defaultTime'  => '09:00',
                         'showMeridian' => false,
                         'showSeconds'  => false,
                         'minuteStep'   => 5,
                     ]
                 ]) ?>
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
            <div class="col-md-12 col-xs-8">
                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? '追加' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
        <?php $form->end(); ?>
    </div>
</div>
