<?php
use \yii\helpers\ArrayHelper;
use \yii\helpers\Html;
use \yii\helpers\Url;
use \common\models\Branch;
use common\models\sodan\Homoeopath;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/calendar/delete.php $
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

$b = [['label' => '全拠点','url'=>['delete','hpath_id'=>$hpath_id], 'active' => ! Yii::$app->request->get('branch_id')]];
foreach($query->all() as $model)
    $b[] = ['label' => $model->nickname,'url'=>['delete','branch_id'=>$model->branch_id,'hpath_id'=>$hpath_id],'active'=>$model->branch_id == Yii::$app->request->get('branch_id')];
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
$jscode = "
sessionStorage.clear();

var cal = $('#y2fc');
cal.fullCalendar({
    lang: 'ja',
    events: '{$ajaxUrl}',
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
        if (calEvent.status !== 0) {
            alert('確定している相談会のため、削除できません。');
            return false;
        }

        // 背景色を調整
        if ($(this).hasClass('delete-interview')) {
            $(this).removeClass('delete-interview');
            $(this).css('background', 'white');
            sessionStorage.removeItem(calEvent.id);
        } else {
            $(this).addClass('delete-interview');
            $(this).css('background', '#d9534f');
            sessionStorage.setItem(calEvent.id, 'true');
        }

        return false;
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
$('#external-remarks .fc-event').each(function() {
    // store data so the calendar knows to render an event upon drop
    $(this).data('event', {
        title: '',
        stick: true, // maintain when user navigates (see docs on the renderEvent method)
        start: $(this).text(),
        color: '#5cb85c',
        borderColor: '#4cae4c',
    });
});
";

$this->registerCss($csscode);
$this->registerJs($jscode);

$param_branch_id = ($branch_id) ? "?branch_id={$branch_id}" : '';
$title = '相談枠削除';
$this->params['breadcrumbs'][] = ['label' => '相談枠削除', 'url' => 'delete' . $param_branch_id];
$labels = ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
rsort($labels);
$this->title = $title .' | ' . implode(' | ', $labels) . ' | '. Yii::$app->name;
?>
<script>
    function submitDelete() {
        var num = window.sessionStorage.length;
        if (!num) {
            alert('削除する相談枠を選択してください。');
            return false;
        }
        if (!confirm('相談枠を削除してよろしいですか？')) {
            return false;
        }
        var i;
        for(i = 0; i < num; i++){

            // 位置を指定して、ストレージからキーを取得する
            var name = window.sessionStorage.key(i);

            // ストレージデータhiddenタグを作成
            $("<input>", {
                type: 'hidden',
                name: 'itv_id[]',
                value: name
            }).appendTo('form#submit-delete');
        }
        $('form#submit-delete').submit();
    }
</script>

<div class="col-md-12">


<div id="hoge2" class="col-md-12">

<?php $form = \yii\bootstrap\ActiveForm::begin([
    'id' => 'submit-delete'
]); ?>
    <?= Html::tag('button', '削除', ['class' => "pull-right btn btn-danger", 'onclick' => 'return submitDelete();']) ?>
<?php $form->end(); ?>

<p class="help-block">
相談枠を削除できます。
削除したい相談枠をクリックし、「削除」をクリックすると背景の赤い相談枠が削除されます。
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
