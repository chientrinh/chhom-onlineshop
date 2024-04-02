<?php
use \yii\helpers\ArrayHelper;
use \yii\helpers\Html;
use \yii\helpers\Url;
use \common\models\Branch;
use common\models\sodan\Homoeopath;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/calendar/index.php $
 * $Id: index.php 3851 2018-04-24 09:07:27Z mori $
 */

// get Array of Homoeopaths
$query  = Homoeopath::find()->with('customer')->active()->multibranch(Yii::$app->request->get('branch_id'))->orderBy(['homoeopath_id' => SORT_ASC]);
if($hpath_id)
{
    $hpath_info = Homoeopath::find()->with('customer')->where(['homoeopath_id' => $hpath_id])->one();
    $hpath = Homoeopath::find()->with('customer')->where(['homoeopath_id' => $hpath_id, 'branch_id' => Yii::$app->request->get('branch_id')])->one();

    $this->params['breadcrumbs'][] = ['label' => $hpath_info->customer->name01 . $hpath_info->customer->name02, 'url' => ['homoeopath/view','id' => $hpath_id]];
    $hpaths = ArrayHelper::map([ $hpath ],'homoeopath_id','customer.homoeopathname');
}
else
    $hpaths = ArrayHelper::merge([''], ArrayHelper::map($query->all(),'homoeopath_id','customer.homoeopathname'));
// now stored in $hpaths

// get Array of Branches
$query  = Branch::find()->center();
$branches = ArrayHelper::map($query->all(),'branch_id','nickname');

$b = [['label' => '全拠点','url'=>['index','hpath_id'=>$hpath_id], 'active' => ! Yii::$app->request->get('branch_id')]];
foreach($query->all() as $model)
    $b[] = ['label' => $model->nickname,'url'=>['index','branch_id'=>$model->branch_id,'hpath_id'=>$hpath_id],'active'=>$model->branch_id == Yii::$app->request->get('branch_id')];
// now stored in $b

$csscode = "
.holiday {
    background-color : #ccccb3;
}

#external-remarks .fc-event {
background-color: #5cb85c;
border: 1px solid #4cae4c;
margin-bottom: 1px;
}
";

$branch_id = Yii::$app->request->get('branch_id', 0);
$ajaxUrl = Url::to(['fetch-data','branch_id'=>$branch_id,'hpath_id'=>$hpath_id]);
$postUrl = Url::to(['create-interview']);
$jscode = "
$('button').click(function () {
    $('#hidden-frame').show();
    $(this).hide();
});

var cal = $('#y2fc');
cal.fullCalendar({
    lang: 'ja',
    events: '$ajaxUrl',
    editable: true,
    droppable: true,
    timeFormat: 'HH:mm',
    timezone: 'local',
    start: '09:30',
    end:   '17:30',
    dow: [ 3/*Wed*/, 4, 5, 6, 7/*Sun*/ ],
    defaultView: 'month',
    header: {
        left:  'today month agendaWeek agendaDay',
        right: 'prev next',
        center: 'title'
    },

    eventClick: function(calEvent, jsEvent, view)
    {
        if(calEvent.url)
        {
            window.location.href = calEvent.url;
            return;
        }

        var obj   = new Date(calEvent.start);
        var date  = obj.toISOString().slice(0, 10);
        var time  = obj.toTimeString().slice(0,5);
        var hpath = $('#hpath option:selected').val();
        var branch = $branch_id;
        var data = {
                    'itv_date': date,
                    'itv_time': time,
                    'branch_id': branch,
                    'homoeopath_id': hpath,
                    'duration': 60,
        };
        $.ajax({
            type: 'POST',
            url: '$postUrl',
            data: data,
            success: function(result)
            {
                var newEvent = JSON.parse(result);
                calEvent.title = newEvent.title;
                calEvent.url   = newEvent.url;
                cal.fullCalendar('updateEvent', calEvent);
            },
            error: function(result)
            {
               var error_msg = result.responseText.replace('Bad Request (#400): ', '').replace(';', '\\n');
               alert(error_msg);
            }
        });
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

    drop: function( date, jsEvent, ui, resourceId )
    {

    }

});
$('#external-remarks .fc-event').each(function() {
    // store data so the calendar knows to render an event upon drop
    $(this).data('event', {
        title: '',
        stick: true, // maintain when user navigates (see docs on the renderEvent method)
        start: $(this).text(),
        color: '#5cb85c',
        borderColor: '#4cae4c',
    });

    // make the event draggable using jQuery UI
    $(this).draggable({
        zIndex: 999,
        revert: true,      // will cause the event to go back to its
        revertDuration: 0  //  original position after the drag
    });
});
";

$this->registerCss($csscode);
$this->registerJs($jscode);

$title = 'カレンダー';
$labels = ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
rsort($labels);
$this->title = $title .' | ' . implode(' | ', $labels) . ' | '. Yii::$app->name;
$param_branch_id = ($branch_id) ? "?branch_id={$branch_id}" : '';
?>

<div class="col-md-12">


<div id="hoge2" class="col-md-12">

<?php if (Yii::$app->id === 'app-backend'):?>
    <?php if(Yii::$app->request->get('branch_id')): ?>
        <?= Html::tag('button', '追加', ['class'=>"pull-right btn btn-success"]) ?>
    <?php endif ?>
    <?= Html::a('相談枠の削除', ['delete' . $param_branch_id], ['class' => 'pull-right btn btn-danger', 'style' => 'margin-right:15px;']) ?>
<?php endif;?>

<div id="hidden-frame" class="row" style="display:none">
<div id="external-remarks" class="col-md-1 pull-right">
    <div class="fc-event ui-draggable ui-draggable-handle">09:30</div>
    <div class="fc-event ui-draggable ui-draggable-handle">10:30</div>
    <div class="fc-event ui-draggable ui-draggable-handle">11:30</div>
    <div class="fc-event ui-draggable ui-draggable-handle">12:30</div>
    <div class="fc-event ui-draggable ui-draggable-handle">14:30</div>
    <div class="fc-event ui-draggable ui-draggable-handle">15:30</div>
    <div class="fc-event ui-draggable ui-draggable-handle">16:30</div>
</div>
<div class="col-md-3 pull-right">
    <?= Html::dropDownList('homoeopath_id',null,$hpaths,['id'=>'hpath','class'=>'form-control']) ?>
</div>
<p class="help-block">
相談会を追加できます。
開始時刻ボタンをマス目にドラッグし、ホメオパスを選択してから、追加したボタンをクリックすると確定します。
なお、休業日のマス目には相談会を追加することができません。
</p>
</div>

 <?= \yii\bootstrap\Nav::widget([
     'options' => ['class'=>"nav nav-tabs"],
     'items'=> $b,
 ]) ?>
</div>

  <?= \yii2fullcalendar\yii2fullcalendar::widget([
      'id'         => 'y2fc',
      'events'     => [],
  ]);
?>
</div>

<div class="col-md-2 col-xs-4">
<p class="fc-day-grid-event fc-event" style="background-color:white;border-color:black;color:black">空き</p>
<p class="fc-day-grid-event fc-event" style="background-color: #3a87ad;color:white">予約済み</p>
<p class="fc-day-grid-event fc-event" style="background-color:#ccccb3;border-color:#ccccb3;color:black">休業日</p>
</div>

 <div class="col-md-2 pull-right text-right">
 <?= Html::a('休業日を編集',['holiday','hpath_id'=>$hpath_id]) ?>
 </div>
