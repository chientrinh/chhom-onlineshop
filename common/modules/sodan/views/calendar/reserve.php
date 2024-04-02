<?php
use \yii\helpers\ArrayHelper;
use \yii\helpers\Html;
use \yii\helpers\Url;
use \common\models\Branch;
use common\models\sodan\Homoeopath;
use common\models\sodan\Client;
use common\models\sodan\Interview;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/calendar/reserve.php $
 * $Id: index.php 2354 2016-04-01 03:58:42Z mori $
 */

$branch_id = Yii::$app->request->get('branch_id', 0);
// get Array of Homoeopaths
$query  = Homoeopath::find()->with('customer')->active()->multibranch($branch_id)->orderBy(['homoeopath_id' => SORT_ASC]);
$hpaths = ArrayHelper::merge([''], ArrayHelper::map($query->all(), 'homoeopath_id', 'customer.homoeopathname'));

$clients[''] = '';
$query = Client::find()->active();
if ($branch_id)
    $query->andWhere(['branch_id' => $branch_id]);

if ($client_list = $query->asArray()->all()) {
    foreach ($client_list as $client) {
        $clients[$client['client_id']] = ($client['customer']['name01'] && $client['customer']['name02']) ? "{$client['customer']['name01']} {$client['customer']['name02']}" : '';
        $clients[$client['client_id']] .= ($client['customer']['kana01'] && $client['customer']['kana02']) ? "（{$client['customer']['kana01']} {$client['customer']['kana02']}）" : '';
        $clients[$client['client_id']] .= ($client['customer']['birth']) ? date('Y/m/d', strtotime($client['customer']['birth'])) : '';
        $clients[$client['client_id']] .= ($client['ng_flg']) ? " 公開NG" : " 公開OK";
        $clients[$client['client_id']] .= " TEL：{$client['customer']['tel01']}{$client['customer']['tel02']}{$client['customer']['tel03']}";
    }
}
asort($clients);

// get Array of Branches
$query  = Branch::find()->center();
$branches = ArrayHelper::map($query->all(),'branch_id', 'nickname');
$b = [['label' => '全拠点', 'url' => ['reserve', 'hpath_id' => $hpath_id], 'active' => !$branch_id]];
foreach($query->all() as $branch)
    $b[] = ['label' => $branch->nickname, 'url' => ['index', 'branch_id' => $branch->branch_id, 'hpath_id' => $hpath_id], 'active' => $branch->branch_id == $branch_id];

$csscode = "
.holiday {
    background-color : #ccccb3;
}
#external-remarks .fc-event {
    background-color: #5cb85c;
    border: 1px solid #4cae4c;
    margin-bottom: 1px;
}
body {
  position:relative;
}

/* モーダルウィンドウのスタイル */
.modal {
  width:100%;
  height:100vh;
  display:none;
}

/* オーバーレイのスタイル */
.overLay {
  position:absolute;
  top:0;
  left:0;
  background:rgba(200,200,200,0.9);
  width:100%;
  height:100vh;
  z-index:10;
}

/* モーダルウィンドウの中身のスタイル */
.modal .inner {
  position:absolute;
  z-index:11;
  top:50%;
  left:50%;
  transform:translate(-50%,-50%);
}

#calendar .fc-time {
  display:none;
}

#day-calendar .fc-content .fc-time {
  font-size:15pt;
}
#day-calendar .fc-title {
  font-size:14pt;
}
";

$ajaxUrl = Url::to(['fetch-data', 'branch_id' => $branch_id, 'hpath_id' => $hpath_id]);
$countUrl = Url::to(['fetch-count', 'branch_id' => $branch_id, 'hpath_id' => $hpath_id]);
$postUrl = Url::to(['create-interview']);
$itvUrl  = Url::to(['fetch-interview']);
$currentUrl = Url::to(['reserve']);
$jscode = "
var cal = $('#y2fc');
cal.fullCalendar({
    lang: 'ja',
    locale:'ja',
    axisFormat:'HH:mm',
    events: '$countUrl',
    editable: true,
    droppable: true,
    timeFormat: 'HH:mm',
    timezone: 'local',
    start: '09:30',
    end:   '17:30',
    dow: [ 3/*Wed*/, 4, 5, 6, 7/*Sun*/ ],
    defaultView: 'month',
    defaultDate: '$start_date',
    header: {
        right: 'prev next',
    },

    dayClick: function(date, jsEvent, view) {
        window.location.href = '$currentUrl' + '?start_date=' + date.format() + '&hpath_id=' + '$hpath_id' + '&client_id=' + $('select[name=client_id]').val() + '#day-calendar';
    },

    dayRender: function (date, cell) {
        console.log(date, cell);
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
});
$('#timeline').fullCalendar({
    lang: 'ja',
    locale:'ja',
    slotLabelFormat:'HH:mm',
    axisFormat:'HH:mm',
    events: '$ajaxUrl',
    editable: true,
    droppable: true,
    draggable: true,
    timeFormat: 'HH:mm',
    timezone: 'local',
    defaultDate: '$start_date',
    dow: [3, 4, 5, 6, 7],
    defaultView: 'agendaDay',
    minTime: '09:00:00',
    maxTime: '18:00:00',
    slotDuration: '00:10:00',
    scrollTime: '03:00:00',
    header: {
        right: '',
    },
    eventDrop: function(event, delta, revertFunc, jsEvent, ui, view) {
        // ***** ここにドラッグ完了時の処理を書く *****
    },
    dayClick:function(date, jsEvent, view){
        $('input[name=\'Interview[itv_id]\']').val('');
        $('select[name=\'Interview[branch_id]\']').val('$branch_id');
        $('input[name=\'Interview[itv_date]\']').val(date.format('YYYY-MM-DD'));
        $('input[name=\'Interview[itv_time]\']').val(date.format('HH:mm:ss'));
        $('input[name=\'Interview[duration]\']').val('60');
        $('select[name=\'Interview[homoeopath_id]\']').val($('select[name=homoeopath_id]').val());
        $('select[name=\'Interview[client_id]\']').val($('select[name=client_id]').val());
        $('select[name=\'Interview[product_id]\']').val('');
        $('select[name=\'Interview[status_id]\']').val('0');
        $('select[name=\'Interview[ticket_id]\']').val('');
        $('textarea[name=\'Interview[officer_use]\']').val('');
        $('textarea[name=\'Interview[note]\']').val('');
        $('#regist-modal').css('margin-top', Math.max(0, ($(window).height() - $('#regist-modal').height()) / 2));
        $('#regist-modal').fadeIn();
        $('#regist-modal').addClass('open');
        return false;
    },
    eventClick:function(calEvent, jsEvent, view){
        $.ajax({
            type: 'POST',
            url: '$itvUrl',
            data: {
              itv_id : calEvent.id
            }
        }).done(function(result) {
            var itv = JSON.parse(result);
            // 顧客情報
            var client = '';
            if (itv.client_id) {
                if (itv.sex_id ==1)
                    var sex = '男性';
                else if (itv.sex_id == 2)
                    var sex = '女性';
                else
                    var sex = '不明';
                var skype = (itv.skype) ? itv.skype : '';
                var name = itv.name01 + ' ' + itv.name02 + ' (' + itv.kana01 + ' ' + itv.kana02 + ')' + ' ' + sex + ' ' + skype;
                var birthday = new Date(itv.birth);
                var birth = birthday.getFullYear() + '年' + (birthday.getMonth() + 1) + '月' + birthday.getDate() + '日' + ' (' + getAge(itv.birth) + ' 才)';
                var tel = itv.tel01 + itv.tel02 + itv.tel03;
                var address = (itv.pref_name) ? itv.pref_name + itv.addr01 + itv.addr02 : '';
                client = name + '<br>' + birth + '<br>' + tel + '<br>' + address;
            }
            var product = (itv.product_name) ? itv.product_name : '';
            var homoeopath = (itv.homoeopathname) ? itv.homoeopathname : itv.homoeopath_name2;
            var officer_use = (itv.officer_use) ? itv.officer_use.replace(/\\r?\\n/g, '</br>') : '';
            var note = (itv.note) ? itv.note.replace(/\\r?\\n/g, '<br>') : '';
            // 詳細モーダルへの値挿入
            $('td.modal-itv-date').text(itv.itv_date + ' ' + itv.itv_time + ' (' + itv.duration + ' 分)');
            $('td.modal-client').html(client);
            $('td.modal-homoeopath').html(homoeopath);
            $('td.modal-product').html(itv.product_name);
            $('td.modal-officer-use').html(officer_use);
            $('td.modal-note div').html(note);
            // 編集モーダルへの値挿入
            $('input[name=\'Interview[itv_id]\']').val(itv.itv_id);
            $('select[name=\'Interview[branch_id]\']').val(itv.branch_id);
            $('input[name=\'Interview[itv_date]\']').val(itv.itv_date);
            $('input[name=\'Interview[itv_time]\']').val(itv.itv_time);
            $('input[name=\'Interview[duration]\']').val(itv.duration);
            $('select[name=\'Interview[homoeopath_id]\']').val(itv.homoeopath_id);
            if (itv.client_id)
                $('select[name=\'Interview[client_id]\']').val(itv.client_id);
            else
                $('select[name=\'Interview[client_id]\']').val($('select[name=client_id]').val());
            $('select[name=\'Interview[product_id]\']').val(itv.product_id);
            $('select[name=\'Interview[status_id]\']').val(itv.status_id);
            $('select[name=\'Interview[ticket_id]\']').val(itv.ticket_id);
            $('textarea[name=\'Interview[officer_use]\']').val(itv.officer_use);
            $('textarea[name=\'Interview[note]\']').val(itv.note);
        }).fail(function(result) {
            alert('該当データがありませんでした');
        });
        $('#detail-modal').css('margin-top', Math.max(0, ($(window).height() - $('#detail-modal').height()) / 2));
        $('#detail-modal').fadeIn();
        $('#detail-modal').addClass('open');
        return false;
    },
});
$('.modalClose').click(function(){
    $(this).parents('.modal').fadeOut();
    $('.modalOpen').removeClass('open');
    return false;
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
if ($('select[name=client_id]').val()) {
    $('#client-itv').empty();
    var client_id = $('select[name=client_id]').val();
    $.ajax({
        type: 'POST',
        url: '$itvUrl',
        data: {
          client_id : client_id
        }
    }).done(function(result) {
        var itvs = JSON.parse(result);
        var table = $('<table class=\'table table-striped table-bordered\'>')
        var thead = $('<thead>');
        var tbody = $('<tbody>')
        thead.append('<tr><th>日次</th><th>ホメオパス名</th><th>種別</th><th>ステータス</th></tr>');

        for (var i in itvs) {
            if (itvs[i] && typeof itvs[i] == 'object') {
                var cancel = (itvs[i].status_name == '予約キャンセル') ? ' style=\"background:gray;\"' : '';
                var html = '<tr' + cancel + '>'
                         + '<td>' + itvs[i].itv_date + '</td>'
                         + '<td>' + itvs[i].homoeopath + '</td>'
                         + '<td>' + itvs[i].product_name + '</td>'
                         + '<td>' + itvs[i].status_name + '</td>'
                         + '</tr>';
                tbody.append(html);
            }
        }
        table.append(thead);
        table.append(tbody);
        if (itvs.parent)
            $('#client-itv').append('<h3>親会員：' + itvs.parent_name + '<br>TEL：' + itvs.parent_tel + '</h3>');
        $('#client-itv').append(table);
    }).fail(function(result) {
        alert('該当データがありませんでした');
    });
}
$('select[name=client_id]').change(function(){
    $('#client-itv').empty();
    var client_id = $(this).val();
    $.ajax({
        type: 'POST',
        url: '$itvUrl',
        data: {
          client_id : client_id
        }
    }).done(function(result) {
        var itvs = JSON.parse(result);

        // 担当ホメオパスを設定してリロード
        if (itvs.charge_homoeopath)
            window.location.href = '$currentUrl' + '?hpath_id=' + itvs.charge_homoeopath + '&start_date=' + '$start_date' + '&client_id=' + client_id;

        var table = $('<table class=\'table table-striped table-bordered\'>')
        var thead = $('<thead>');
        var tbody = $('<tbody>')
        thead.append('<tr><th>日次</th><th>ホメオパス名</th><th>種別</th><th>ステータス</th></tr>');

        for (var i in itvs) {
            if (itvs[i] && typeof itvs[i] == 'object') {
                var html = '<tr>'
                         + '<td>' + itvs[i].itv_date + '</td>'
                         + '<td>' + itvs[i].homoeopath + '</td>'
                         + '<td>' + itvs[i].product_name + '</td>'
                         + '<td>' + itvs[i].status_name + '</td>'
                         + '</tr>';
                tbody.append(html);
            }
        }
        table.append(thead);
        table.append(tbody);
        if (itvs.parent)
            $('#client-itv').append('<h3>親会員：' + itvs.parent_name + '<br>TEL：' + itvs.parent_tel + '</h3>');
        $('#client-itv').append(table);
    }).fail(function(result) {
        alert('該当データがありませんでした');
    });
});

$('select[name=homoeopath_id]').change(function(){
   window.location.href = '$currentUrl' + '?hpath_id=' + $(this).val() + '&start_date=' + '$start_date' + '&client_id=' + $('select[name=client_id]').val();
});

// フィルタ設定
var homoeopathArray = new Array();
var clientArray = new Array();

$('select[name=homoeopath_id]').children().each(function(){
   homoeopathArray.push( { value:$(this).val(), body:$(this).html() });
});

$('select[name=client_id]').children().each(function(){
   clientArray.push( { value:$(this).val(), body:$(this).html() });
});

$('#homoeopath-filter').keyup(function(){
    option_filter($(this).val(), homoeopathArray, 'homoeopath_id');
});

$('#client-filter').keyup(function(){
    option_filter($(this).val(), clientArray, 'client_id');
});
";

echo <<<EOF
<script>
function option_filter (s, options, attr) {
    $('select[name=' + attr + ']').empty();
    if (s == ''){
       $(options).each(function(i, o){
          $('select[name=' + attr + ']').append( $('<option>').val(o.value).text(o.body));
       });
    } else {
       $('select[name=' + attr + ']').append( $('<option>').val('').text(''));
       options.filter(function(o, i){
          if (o.body.toLowerCase().indexOf(s.toLowerCase()) != -1){
             $('select[name=' + attr + ']').append( $('<option>').val(o.value).text(o.body));
          }
       });
    }
}

function openRegistModal() {
    $('#detail-modal').fadeOut();
    $('.modalOpen').removeClass('open');
    $('#regist-modal').css('margin-top', Math.max(0, ($(window).height() - $('#detail-modal').height()) / 2));
    $('#regist-modal').fadeIn();
    $('#regist-modal').addClass('open');
    return false;
}

function getAge(birthday){

  //誕生年月日
  var birthday  = new Date(birthday);

  //今日
  var today = new Date();

  //今年の誕生日
  var thisYearBirthday = new Date(today.getFullYear(), birthday.getMonth(), birthday.getDate());

  //今年-誕生年
  var age = today.getFullYear() - birthday.getFullYear();

  //今年の誕生日を迎えていなければage-1を返す
  return (today < thisYearBirthday) ? age - 1 : age;
}
</script>
EOF;

$this->registerCss($csscode);
$this->registerJs($jscode);

$title = 'カレンダー';
$labels = ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
rsort($labels);
$this->title = $title .' | ' . implode(' | ', $labels) . ' | '. Yii::$app->name;
$param_branch_id = ($branch_id) ? "?branch_id={$branch_id}" : '';
$this->params['breadcrumbs'][] = ['label' => "カレンダー予約", 'url' => ['reserve' . $param_branch_id]];
?>

<div class="col-md-12">
    <?= Html::errorSummary($model,['class'=>'alert alert-danger']) ?>
    <div class="col-md-6">
        <div class="col-md-5">
            <?= Html::tag('label', 'クライアント') ?>
        </div>
        <div class="col-md-5">
            <?= Html::textInput('client-filter', '', [
                'class' => 'form-control',
                'id' => 'client-filter',
                'autocomplete' => 'off',
                'style' => 'margin:5px 50%;'
            ]) ?>
        </div>
        <?= Html::dropDownList('client_id', $client_id, $clients, ['label' => false, 'class' => 'form-control', 'style' => 'margin-bottom:5px;']) ?>
        <div id="client-itv"></div>
    </div>
    <div id="calendar" class="col-md-6">
        <div class="col-md-5">
            <?= Html::tag('label', 'ホメオパス') ?>
        </div>
        <div class="col-md-5">
            <?= Html::textInput('homoeopath-filter', '', [
                'class' => 'form-control',
                'id' => 'homoeopath-filter',
                'autocomplete' => 'off',
                'style' => 'margin:5px 50%;'
            ]) ?>
        </div>
        <?= Html::dropDownList('homoeopath_id', $hpath_id, $hpaths, ['label' => false, 'class' => 'form-control', 'style' => 'margin-bottom:5px;']) ?>
        <?= \yii2fullcalendar\yii2fullcalendar::widget([
            'id'         => 'y2fc',
            'events'     => [],
        ]); ?>
    </div>
    <?php if ($hpath_id): ?>
        <h2><?= $hpaths[$hpath_id] ?>のスケジュール</h2>
    <?php endif;?>
    <div id="day-calendar" class="col-md-12" style="margin-top: 10px;">
        <?= \yii2fullcalendar\yii2fullcalendar::widget([
            'id'         => 'timeline',
            'events'     => [],
        ]); ?>

        <?php
            // 時間外の相談会情報取得
            $query = Interview::find()->where(['itv_date' => $start_date, 'itv_time' => '00:00:00']);
            if ($hpath_id)
                $query->andWhere(['homoeopath_id' => $hpath_id]);
        ?>
        <h3>メール相談</h3>
        <?= \yii\grid\GridView::widget([
            'id' => 'grid-recipe',
            'dataProvider'=> new \yii\data\ActiveDataProvider([
                'query' => $query,
                'sort'  => false,
            ]),
            'layout' => '{items}{pager}',
            'columns' => [
                [
                    'attribute' => 'itv_id',
                    'format'    => 'raw',
                    'value'     => function($data)
                    {
                        return Html::a($data->itv_id, ['/sodan/interview/view', 'id' => $data->itv_id]);
                    },
                ],
                [
                    'attribute' => 'homoeopath_id',
                    'value'     => function($data)
                    {
                        return $data->homoeopath->homoeopathname;
                    },
                ],
                [
                    'attribute' => 'client_id',
                    'value'     => function($data)
                    {
                        if ($data->client)
                            return "{$data->client->name} （{$data->client->kana}）";

                        return null;
                    },
                ],
                [
                    'attribute' => 'product_id',
                    'value'     => function($data)
                    {
                        if ($data->product)
                            return $data->product->name;

                        return null;
                    },
                ],
                [
                    'attribute' => 'officer_use',
                    'format'    => 'html',
                    'value'     => function($data)
                    {
                        return $data->officer_use;
                    },
                ],
            ],
        ]) ?>
    </div>
</div>

<!-- 詳細モーダルウィンドウ -->
<div class="modal" id="detail-modal">
    <!-- モーダルウィンドウが開いている時のオーバーレイ -->
    <div class="overLay modalClose"></div>
    <!-- モーダルウィンドウの中身 -->
    <div class="inner" style="width:85%;background: white;padding: 15px;">
        <div class="interview-form">
            <table class="table table-striped table-bordered detail-view">
                <tbody>
                    <tr><th>相談日</th><td class="modal-itv-date"></td></tr>
                    <tr><th>ホメオパス</th><td class="modal-homoeopath"></td></tr>
                    <tr><th>クライアント</th><td class="modal-client"></td></tr>
                    <tr><th>相談種別</th><td class="modal-product"></td></tr>
                    <tr><th>事務欄</th><td class="modal-officer-use"></td></tr>
                    <tr><th>備考</th><td class="modal-note"><div style="height:100px; width:100%; overflow-x:hidden;"></div></td></tr>
                </tbody>
            </table>

            <div class="col-md-12 col-xs-8">
                <div class="form-group">
                    <a href="" class="modalOpen btn btn-success" onclick="return openRegistModal();">修正</a>
                    <a href="" class="modalClose btn btn-danger">閉じる</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 登録・編集モーダルウィンドウ -->
<div class="modal" id="regist-modal">
    <!-- モーダルウィンドウが開いている時のオーバーレイ -->
    <div class="overLay modalClose"></div>
    <!-- モーダルウィンドウの中身 -->
    <div class="inner" style="width:85%;background: white;">
        <?= $this->render('_form', [
            'model' => $model,
            'branch_id' => $branch_id,
            'itv_date' => $start_date
        ]) ?>
    </div>
</div>
