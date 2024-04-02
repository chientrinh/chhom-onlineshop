<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/client/kami-karute.php $
 * $Id: kami-karute.php 3851 2018-04-24 09:07:27Z mori $
 */

use \yii\helpers\Html;
use \yii\helpers\Url;
use \common\models\Customer;
use common\models\sodan\Interview;

function imageData($basename)
{
    $filename = __DIR__ .'/'. $basename;
    $binary   = file_get_contents($filename);
    $type     = pathinfo($filename, PATHINFO_EXTENSION);
    $ascii    = 'data:image/' . $type . ';base64,' . base64_encode($binary);

    return $ascii;
}

$this->params['breadcrumbs'] = [];

Yii::$app->formatter->nullDisplay = '--';

$customer = Customer::findOne($model->client_id);
if($customer->sex)
    $sex = $customer->sex->name;
else
    $sex = '不明';

$week_list = ["日", "月", "火", "水", "木", "金", "土"];
$itv = ($itv_id) ? Interview::findOne($itv_id) : null;

if (!$itv) {
    throw new \yii\web\NotFoundHttpException('相談会が存在しません');
}
$itv_date = new DateTime($itv->itv_date . $itv->itv_time);
?>

<style type="text/css">
body {
   font-size: 10pt;
   font-family: Meiryo;
}
table {
   width: 100%;
   margin-bottom: 1mm;
   border-collapse: collapse;
}
caption, .caption {
   font-size: 9pt;
   text-align: left;
}
td {
   padding: 1mm;
   border: 1px solid;
   border-collapse: collapse;
   vertical-align: top;
}
</style>

<p>
    <?= $model->getInterviews()->active()->today(false)->past()->exists() ? "再" : "初" ?>）
    <?= "{$itv_date->format('Y年m月d日')}({$week_list[(int)$itv_date->format('w')]}曜日)（{$itv_date->format('H時i分')}）" ?>&nbsp;&nbsp;
    担当: <?= $itv->homoeopath->homoeopathname ?>
</p>

<table>
  <tr>
    <td height="12mm" width="35%" valign="top">ﾌﾘｶﾞﾅ　<?= $customer->kana ?><br>氏名：　<strong style="font-size: 14pt;"><?= $customer->name ?></strong></td>
    <td height="12mm" width="15%" valign="top">性別：<br>　<span style="font-size:12pt;"><?= $sex ?></span></td>
    <td height="12mm" width="35%" valign="top">生年月日<br><span style="font-size:14pt;"><?= date('Y年m月d日', strtotime($customer->birth)) . "（{$customer->age}歳）" ?></span></td>
    <td height="12mm" width="15%" valign="top">職業</td>
  </tr>
  <tr>
    <td height="12mm" colspan="2" valign="top" rowspan="2">主訴：<br><?= $itv->complaint ?></td>
    <td colspan="2" height="5mm" valign="top">TEL　<?= $customer->tel ?></td>
  </tr>
  <tr>
    <td colspan="2" height="5mm" valign="top">住所：<?= $customer->pref->name . $customer->addr01 ?></td>
  </tr>
</table>

<table>
  <tr>
    <td colspan="3" height="25mm" valign="top">変化(　　　　　　　　　　)RXS後</td>
  </tr>
</table>

<table>
 <tr>
    <td height="37mm" width="40%">処方：　　随時</td>
    <td width="20%">朝</td>
    <td width="20%">昼</td>
    <td width="20%">夜</td>
  </tr>
</table>

<table>
<caption>
    主訴の付随する症状の部位、基調＜　＞・時間＜　＞・姿勢＜　＞・環境＜　＞・感覚
</caption>
  <tr>
    <td width="33%"><img src="<?= imageData('face.jpg') ?>" height="160px" ></td>
    <td width="33%"><img src="<?= imageData('front.jpg')?>" height="25%"></td>
    <td width="34%"><img src="<?= imageData('back.jpg') ?>" height="25%"></td>
  </tr>
</table>

<table>
    <tr>
        <td height="40mm">
            <caption>
                General(総体)（基調＜　＞、食欲、切望、嫌悪、喉の乾き、汗、尿、便、生理、季節、天候、睡眠、特徴的、原因、付随）
            </caption>
        </td>
    </tr>
    <tr>
        <td height="40mm">
            <span class="caption">
                Mental(精神)（基調＜　＞、恐怖、不安、心配、葛藤、イライラ、罪悪感、自殺傾向、失恋、夢、希望、旅行、特徴的、原因、付随）            </span>
        </td>

    </tr>
</table>

<table>
    <tr>
        <td height="20mm">
            <span class="caption">遺伝傾向</span>
            <table>
                <tr>
                    <td style="border:0">
                        母<br>
                        母母　　　　　　母父　　　　　<br>
                        兄弟
                    </td>
                    <td style="border:0">
                        父<br>
                        父母　　　　　　父父　　　　　
                    </td>
                </tr>
            </table>
        </td>
        <td rowspan="3" width="45%">
            <span class="caption">QX</span>
        </td>
    </tr>
    <tr>
        <td height="12mm">
            <span class="caption">薬の使用</span>
        </td>
    </tr>
    <tr>
        <td height="12mm">
            <span class="caption">ホメオパシー的診断の理由と覚書</span>
        </td>
    </tr>
</table>
