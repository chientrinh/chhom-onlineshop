<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/recipe/views/resrve.php $
 * $Id: print.php 3670 2017-10-13 09:47:41Z naito $
 */
use common\models\sodan\BookTemplate;
use common\models\sodan\Holiday;

include '/usr/share/pear/Date.php';
include '/usr/share/pear/Date/Holidays.php';

$week_list = ["日", "月", "火", "水", "木", "金", "土"];

// 予約日の（月曜・火曜・祝日・全拠点休業日を除く）３日前をキャンセル期限にする
$date_holidays = \Date_Holidays::factory('Japan');
$date = new Date();
$date->setDate($model->itv_date);

$cancel_date = date('Y-m-d', strtotime($model->itv_date));
$cnt = 0;
while($cnt < 3) {
    $week_day_flg = true;

    $cancel_date = date('Y-m-d', strtotime('-1 day', strtotime($cancel_date)));
    $cancel_datetime = new DateTime($cancel_date);
    $cancel_w = (int)$cancel_datetime->format('w');

    // 月曜・火曜を除く
    if ($week_list[$cancel_w] === '月' || $week_list[$cancel_w] === '火') {
        $week_day_flg = false;
    }
    // DateHolidaysにある祝日を除く
    if ($date_holidays->isHoliday($cancel_date)) {
        $week_day_flg = false;
    }
    // sodan_holidayのある全拠点休業日を除く
    if (Holiday::find()->where(['date' => $cancel_date, 'homoeopath_id' => NULL, 'active' => 1, 'holiday_flg' => 1])->one()) {
        $week_day_flg = false;
    }
    if ($week_day_flg) {
        $cnt++;
    }
}

// 予約日の曜日取得
$datetime = new DateTime($model->itv_date);
$w = (int)$datetime->format('w');

$template = ($template_id) ? BookTemplate::find()->where(['template_id' => $template_id])->one() : null;
?>

<style type="text/css">
<!--
body {
   font-size: 12pt;
   font-family: Meiryo;
}
p{
   margin: 0;
   padding: 0;
   font-size: 10pt;
}
table {
   width: 100%;
   border: 1px solid #ddd;
   border-collapse: collapse;
   font-size: 1.7em;
   text-align: center;
}
th {
   margin: 0;
   padding: 0.5%;
   border: 1px solid #ddd;
   font-size: 0.6em;
}
td {
    border: 1px solid #ddd;
    padding: 0.5%;
}
.notice {
   font-size: 0.95em;
   margin-top: 15px;
}
.footer {
    text-align: center;
    font-weight: bold;
    margin-top: 40px;
}
.header {
    font-size:1.0em;
    margin-bottom: 5px;
    float:left;
    width:40%;
}
</style>

<div style="height:3%;">
    <p class="header"><?php echo date('Y/m/d');?></p>
    <p class="header">□フラグ→<br>□チェック&nbsp;&nbsp;&nbsp;□復唱&nbsp;&nbsp;&nbsp;□チェック</p>
</div>
<table>
    <tr>
        <td colspan="2">
            <span style="font-size: 1.2em;">相談会予約票</span>
            <p style="font-size:0.8em;">相談会当日、必ずご持参ください</p>
        </td>
    </tr>
    <tr>
        <th>相談会ご予約名</th>
        <td><?php echo "{$model->client->name01} {$model->client->name02} 様"; ?></td>
    </tr>
    <tr>
        <th>ご住所</th>
        <td><?php echo $model->client->addr; ?></td>
    </tr>
    <tr>
        <th>ご連絡先</th>
        <td><?php echo $model->client->tel; ?></td>
    </tr>
    <tr>
        <th>相談会種別</th>
        <td><?php $name = ($model->product->name) ? $model->product->name : '（未指定）'; echo $name; ?></td>
    </tr>
    <tr>
        <th>担当ホメオパス</th>
        <td><?php echo "<strong>{$model->homoeopath->homoeopathname}</strong> 先生"; ?></td>
    </tr>
    <tr>
        <th>ご予約日時</th>
        <td><?php echo date('Y年m月d日', strtotime($model->itv_date)) . "【{$week_list[$w]}】" . date('H:i', strtotime($model->itv_time)); ?></td>
    </tr>
    <tr>
        <th>キャンセル期限</th>
        <td><?php echo date('Y年m月d日', strtotime($cancel_date)) . "【{$week_list[$cancel_w]}】"; ?> 17:00まで</td>
    </tr>
    <tr>
        <th>事前報告提出期限</th>
        <td><?php echo date('Y年m月d日', strtotime('-1 week', strtotime($model->itv_date))) . "【{$week_list[$w]}】"; ?></td>
    </tr>
</table>

<br>
<?php if ($template):?>
<p class="notice">
    <?php echo nl2br($template->body);?>
</p>
<?php endif;?>
<p class="notice">
    ※予約の変更・キャンセルは健康相談会の3営業日前17時00分までにお電話にてお願いします。
    以降の変更はキャンセル料を頂戴いたします。（月曜・火曜・年末年始はお休みとなりますのでご注意ください。）
</p>
<p class="notice">
    ※日時変更・キャンセル手数料<br>
    大人（中学生以上）：6,000円（税別）<br>
    小人（小学生以下）：動物：4,800円（税別）<br>
    その他コース：相談料の60%
</p>
<p class="notice">
    ※こちらのお客様控えは、お手数をお掛けいたしますが、相談会当日にお持ちいただき、受付にてスタッフにお渡しください。
</p>
<p class="notice">
    ※ご記入いただきました個人情報は、お客様が <?php echo str_replace("日本ホメオパシーセンター", "", $model->branch->name);?>
    へ相談会予約についてお問合せされた場合の本人確認及び
    相談会予約にかかわるお知らせのために使用させていただきます。
</p>
<p class="notice">
    ※当日来場が難しい場合は、電話相談に変更することが可能です。
</p>
<p class="notice footer">
    <?php echo $model->branch->name;?><br>
    TEL：<?php echo $model->branch->tel;?><br>
    （9:30～17:30 月・火曜・祭日を除く）
</p>
