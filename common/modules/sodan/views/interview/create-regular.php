<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\sodan\Homoeopath;
use yii\helpers\Url;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/interview/create-regular.php $
 */
$ajaxUrl = Url::to(['fetch-homoeopath']);
$openUrl = Url::to(['fetch-opentime']);
$jscode = "
$('select[name=\'Interview[branch_id]\']').change(function(){
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
    $('select[name=\'Interview[homoeopath_id]\']').empty();
    $('select[name=\'Interview[homoeopath_id]\']').append($('<option>').text('').val(''));
    for (var i in homoeopaths) {
      if (!homoeopaths[i]) {
        continue;
      }
      plist = $('<option>').text(homoeopaths[i]).val(i);
      $('select[name=\'Interview[homoeopath_id]\']').append(plist);
    }
  }).fail(function(result) {
    alert('データ取得に失敗しました');
  });
});

// 公開枠自動チェック機能
$('select[name=\'Interview[homoeopath_id]\']').change(function(){
  var homoeopath_id = $(this).val();
  $.ajax({
    type: 'POST',
    url: '{$openUrl}',
    data: {
      homoeopath_id : homoeopath_id
    }
  }).done(function(result) {
    var opentimes = JSON.parse(result);
    var time_list = ['09:30', '10:30', '11:30', '12:30', '14:30', '15:30', '16:30'];

    $('input[name^=open_flg]').prop('checked', false);
    for (var i in opentimes) {
        var time = time_list[opentimes[i].week_day];
        for (var j in time_list) {
            if (opentimes[i].start_time <= time_list[j] && opentimes[i].end_time >= time_list[j]) {
                $('input[name=\'open_flg[' + time_list[j] + ']\']').prop('checked', true);
            }
        }
    }
  }).fail(function(result) {
    alert('データ取得に失敗しました');
  });
});

sessionStorage.clear();

var cal = $('#y2fc');
cal.fullCalendar({
    lang: 'ja',
    header: false,
    header: {
        right: 'prev next',
    },

    dayClick: function(date, jsEvent, view)
    {
        var date = date.toISOString().slice(0, 10);
        // 背景色を調整
        if ($(this).hasClass('delete-interview')) {
            $(this).removeClass('delete-interview');
            $(this).css('background', 'white');
            sessionStorage.removeItem(date);
        } else {
            $(this).addClass('delete-interview');
            $(this).css('background', 'yellow');
            sessionStorage.setItem(date, 'true');
        }
        return false;
    }
});
";
$this->registerJs($jscode);

echo <<<EOF
<script>
    function submitDay() {
        var num = window.sessionStorage.length;
        if (!num) {
            $('form#submit-regular').submit();
            return false;
        }
        for(var i = 0; i < num; i++){

            // 位置を指定して、ストレージからキーを取得する
            var name = window.sessionStorage.key(i);

            // ストレージデータhiddenタグを作成
            $("<input>", {
                type: 'hidden',
                name: 'day_list[]',
                value: name
            }).appendTo('form#submit-regular');
        }
        $('form#submit-regular').submit();
    }
</script>
EOF;

$query = ($branch_id) ? Homoeopath::find()->active()->multibranch($branch_id) : Homoeopath::find()->active();
$hpath = ArrayHelper::merge(['' => ''], ArrayHelper::map($query->all(),'homoeopath_id','customer.homoeopathname'));
asort($hpath);

$query = \common\models\Branch::find()->center();
$branch = ArrayHelper::merge(['' => ''], ArrayHelper::map($query->all(), 'branch_id', 'name'));

$year_list = [
    date('Y') => date('Y')
];
if (date('Y') !== date('Y', strtotime('+2 month', time()))) {
    $year_list += [date('Y', strtotime('+2 month', time())) => date('Y', strtotime('+2 month', time()))];
}

$month_list = [
    date('F') => date('m'),
    date('F', strtotime('+1 month', time())) => date('m', strtotime('+1 month', time())),
    date('F', strtotime('+2 month', time())) => date('m', strtotime('+2 month', time())),
];

$week_d_list = [
    'sunday'    => '日曜日',
    'monday'    => '月曜日',
    'tuesday'   => '火曜日',
    'wednesday' => '水曜日',
    'Thursday'  => '木曜日',
    'friday'    => '金曜日',
    'saturday'  => '土曜日'
];

$itv_time_list = [
    '09:30' => '09:30',
    '10:30' => '10:30',
    '11:30' => '11:30',
    '12:30' => '12:30',
    '14:30' => '14:30',
    '15:30' => '15:30',
    '16:30' => '16:30'
];

$duration_list = [
    '09:30' => '60',
    '10:30' => '60',
    '11:30' => '60',
    '12:30' => '60',
    '14:30' => '60',
    '15:30' => '60',
    '16:30' => '60'
];

$title = '定期予定';
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['create-regular']];
$labels = ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
rsort($labels);
$this->title = implode(' | ', $labels) . ' | '. Yii::$app->name;
?>

<div class="room-create">
    <p>
        １か月分の相談会を一括作成します。<br>曜日、開始時間、相談時間を指定すると選択した月に毎週分相談枠が作成されます。<br>
        日にちごとに枠を作成したい場合は、作成したいカレンダーの日にちをクリックして開始時間、相談時間を指定して「追加」をクリックしてください。
    </p>
    <div class="interview-form">
        <?php $form = \yii\bootstrap\ActiveForm::begin([
            'id' => 'submit-regular',
            'enableClientScript' => false,
        ]); ?>

        <div class="row col-md-12">
            <div class="col-md-6">
                <?= $form->field($model, 'branch_id')->dropDownList($branch) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'homoeopath_id')->dropDownList($hpath)->hint(false) ?>
            </div>
            <div class="col-md-6">
                <!-- 使っていないらしいので非表示
                <div class="col-md-6" style="padding-left: 0;">
                    <label class="control-label" for="interview-create_y">作成年</label>
                    <?= Html::dropDownList('create_y', Yii::$app->request->post('create_y'), $year_list, ['class' => 'form-control']) ?>
                </div>
                <div class="col-md-6" style="padding-right: 0;">
                    <label class="control-label" for="interview-create_m">作成月</label>
                    <?= Html::dropDownList('create_m', Yii::$app->request->post('create_m'), $month_list, ['class' => 'form-control']) ?>
                </div>
                -->
                <div class="col-md-12" style="margin: 15px 0;">
                    <label class="control-label" for="interview-create_m">個別作成</label>
                    <?= \yii2fullcalendar\yii2fullcalendar::widget([
                        'id'         => 'y2fc',
                    ])?>
                </div>
            </div>
            <div class="col-md-1 form-group">
                <label class="control-label" for="interview-week-d">曜日</label>
                <?= Html::checkBoxList('week_d_list[]', Yii::$app->request->post('week_d_list'), $week_d_list, ['separator' => '<br>'])?>
            </div>
            <div class="col-md-1 form-group">
                <label class="control-label pull-left" for="interview-itv-time" style="width:100%;">公開枠</label>
                <?php foreach ($itv_time_list as $itv_time): ?>
                <div class="pull-left" style="width:90%;margin:3px 0;">
                    <?= Html::hiddenInput("open_flg[{$itv_time}]", 0) ?>
                    <label>
                        <?= Html::checkbox("open_flg[{$itv_time}]", Yii::$app->request->post('open_flg')[$itv_time]) ?>
                        <?= $itv_time ?>～
                    </label>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="col-md-2 form-group">
                <div class="col-md-12">
                    <label class="control-label pull-left" for="interview-itv-time" style="width:100%;">開始時間</label>
                    <?php foreach ($itv_time_list as $itv_time): ?>
                    <div class="pull-left">
                        <?= Html::checkbox("itv_time_check[{$itv_time}]", (Yii::$app->request->post('itv_time_check') && !isset(Yii::$app->request->post('itv_time_check')[$itv_time])) ? false : true) ?>
                    </div>
                    <div class="pull-left" style="width:90%;">
                        <?= \kartik\time\TimePicker::widget(['name' => 'itv_time_list['.$itv_time.']', 'pluginOptions' => [
                            'defaultTime'  => $itv_time,
                            'showMeridian' => false,
                            'showSeconds'  => false,
                            'minuteStep'   => 5,
                        ]]); ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-md-2 form-group">
                <label class="control-label">相談時間</label>
                <?php foreach ($duration_list as $time => $duration):?>
                    <?= Html::textInput("duration_list[{$time}]", Yii::$app->request->post('duration_list')[$time] ? : $duration_list[$time], [
                        'class' => 'form-control',
                        'style' => 'width:50%;'
                    ]) ?>
                <?php endforeach;?>
            </div>

            <div class="col-md-12 col-xs-8">
                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? '追加' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'onclick' => 'return submitDay();']) ?>
                </div>
            </div>
        </div>
        <?php $form->end(); ?>
        <?= Html::errorSummary($model,['class'=>'alert alert-danger']) ?>

    </div>
</div>
