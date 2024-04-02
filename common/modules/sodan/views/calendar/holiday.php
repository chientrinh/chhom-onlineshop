<?php
use \yii\helpers\ArrayHelper;
use \yii\helpers\Html;
use \yii\helpers\Url;
use \backend\models\Staff;
use \common\models\sodan\Homoeopath;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/calendar/holiday.php $
 * $Id: holiday.php 3851 2018-04-24 09:07:27Z mori $
 */

$this->params['breadcrumbs'][] = ['label'=>'休業日の編集','url'=>Url::current()];

$csscode = "
.holiday {
    background-color : #ccccb3;
}
";
if(Yii::$app->user->identity instanceof Staff)
    $hpath_id = isset($hpath_id) ? $hpath_id : '';
else
    $hpath_id = Yii::$app->user->id;

if($hpath_id)
    $this->params['breadcrumbs'][] = ['label'=> ($hpath = Homoeopath::findOne($hpath_id)) ? $hpath->name : null,
                                      'url'  => ['homoeopath/view','id'=>$hpath_id] ];
else
    $this->params['breadcrumbs'][] = ['label'=> 'ホメオパシーセンター本部・全拠点'];

$ajaxUrl = Url::to(['fetch-data','hpath_id'=>$hpath_id]);
$postUrl = Url::to(['toggle-holiday']);
$jscode = "
var cal = $('#y2fc');
cal.fullCalendar({
    lang: 'ja',
    events: '$ajaxUrl',
    editable: true,
    droppable: false,
    timeFormat: 'HH:mm',
    timezone: 'local',
    start: '09:30',
    end:   '17:30',
    dow: [ 3/*Wed*/, 4, 5, 6, 7/*Sun*/ ],
    defaultView: 'month',
    header: {
        left:  '',
        right: 'today prev next',
        center: 'title'
    },

    dayClick: function(date, jsEvent, view)
    {
        if(! $(this).hasClass('fc-future'))
             return;

        var date = date.toISOString().slice(0, 10);
        var data = {
                    'date': date,
                    'hid': '$hpath_id',
        };

        $.ajax({
            type: 'POST',
            url: '$postUrl',
            data: data,
            success: function(result)
            {
               return; /* successを受信後に class='holiday' にすべきだが、よくわからないので保留。*/
            },
            error: function(result)
            {
               alert(JSON.stringify(result));
            }
        });

        /* ajax の結果にかかわらず、背景色を変更する */
        if($(this).hasClass('holiday'))
            $(this).removeClass('holiday');
        else
            $(this).addClass('holiday');
    },

    dayRender: function (date, cell)
    {
        if(cell.hasClass('fc-past'))
            cell.css('background-color', '#f0f0f5');
    },

    eventAfterRender: function( event, element, view )
    {
        if(! event.allDay || event.title)
        return;
        
        var date  = event.start.toISOString().slice(0, 10);
        var param = '[data-date=' + date + ']';
        $(param).addClass('holiday');
    },

});
";

$this->registerCss($csscode);
$this->registerJs($jscode);

?>

<div class="col-md-12">

    <p class="help-block">
        明日以降の日付に対して休業日を編集できます。<br>
        営業日のマス目をクリックすると休業日に、休業日のマス目をクリックすると営業日になります。<br>
        なお、相談会が設定されているマス目は休業日にすることができません。
    </p>

  <?= \yii2fullcalendar\yii2fullcalendar::widget([
      'id'         => 'y2fc',
      'events'     => [],
  ]);
?>

<div class="col-md-2 col-xs-4">
<p class="fc-day-grid-event fc-event" style="background-color:white;border-color:black;color:black">空き</p>
<p class="fc-day-grid-event fc-event" style="background-color:#3a87ad;color:white">予約済み</p>
<p class="fc-day-grid-event fc-event" style="background-color:#ccccb3;border-color:#ccccb3;color:black">休業日</p>
</div>
    <p class="help-block">
        （クリック後のカレンダー表示の実装が不十分のため）少し動作が怪しいので、気になったらページを再読込してください。そうすると最新の情報でカレンダーを再描画します。
    </p>

</div>
