<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/recipe/views/print.php $
 * $Id: print.php 3670 2017-10-13 09:47:41Z naito $
 */
use \common\models\RemedyVial;
?>

<style type="text/css">
<!--
body {
   font-size: 12pt;
   font-family: Meiryo;
}
h1 {
   margin: 0;
   padding: 0;
   font-size: 16pt;
}
p
{
   margin: 0;
   padding: 0;
   font-size: 10pt;
}
table {
   width: 100%;
   border: 0px;
   border-collapse: collapse;
   font-size: 10pt;
}
th {
   margin: 0;
   padding: 0;
   font-size: 12pt;
}
/*th, td {*/
th {
   text-align: left;
   font-size: 10pt;
   vertical-align: top;
   padding: 0;
}

td .homoeo-memo{
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
    width:10%;
    max-width:10%;
}
.remedy_info, .remedy_name
{
   border: 1px solid #000;
   font-size: 14pt;
}
.remedy_info
{
   text-align: center;
}
.notice
{
   font-size: 9pt;
}
.alert-danger {
  color: #a94442;
  background-color: #f2dede;
  border-color: #ebccd1;
}
.alert {
  padding: 15px;
  margin-bottom: 20px;
  border: 1px solid transparent;
  border-radius: 4px;
}
.btn-default {
  color: #333;
  background-color: #fff;
  border-color: #ccc;
}
.btn {
  display: inline-block;
  padding: 6px 12px;
  margin-bottom: 0;
  font-size: 14px;
  font-weight: normal;
  line-height: 1.42857143;
  text-align: center;
  white-space: nowrap;
  vertical-align: middle;
  -ms-touch-action: manipulation;
  touch-action: manipulation;
  cursor: pointer;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  background-image: none;
  border: 1px solid transparent;
  border-radius: 4px;
}
@media print {
form { display:none }
hr { height:1px; border:1px solid #000; padding:0; color:#000; background-color:#000; }
.notice { position: absolute; bottom: 8mm; width: 180mm; }
}
-->
</style>

<?php if($model->isNewRecord): ?>
<div class="alert alert-danger">
<h1>適用書プレビュー</h1>
<p>(この適用書はまだ発行されていないため、印刷できません)</p>
  <?= \yii\helpers\Html::a('戻る', ['/recipe/create/index'],['class'=>'btn btn-default']) ?>
</div>
<?php else: ?>
<form>
   <input type="button" value="このページを印刷" onclick="window.print();" />
</form>
<?php endif ?>

<h1>レメディー適用書</h1>
<span style="font-weight:bold;font-size:12pt">
    No. <?= sprintf('%06d', $model->recipe_id) ?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;パスワード <?= $model->pw ?>
</span>

<table>
<tr>
<td>

  <p>
    <span style="font-weight:bold;font-size:12pt">
      有効期限
      <?= Yii::$app->formatter->asDate($model->expire_date, 'php:Y年 m月 d日 (D)') ?>
    </span>
    ※1
  </p>
<p style="font-size:4pt">&nbsp;</p>
<p style="font-size:12pt">氏名&nbsp;&nbsp;&nbsp;&nbsp;
  <?php if($model->client): /* CLIENT NAME */ ?>
  <?= $model->client->name ?>
  <?php elseif($model->delivery): ?>
  <?= $model->delivery->name ?>
  <?php elseif($model->manual_client_name): /* CLIENT NAME(manual input) */ ?>
  <?= $model->manual_client_name ?>
  <?php else: ?>
  (氏名なし)
  <?php endif ?>

  <?php if($model->client && $model->client->age): /* CLIENT AGE */ ?>
  ( <?= $model->client->age ?> 歳 )
  <?php elseif($model->manual_client_age): /* CLIENT AGE(manual input) */ ?>
  ( <?= $model->manual_client_age ?> 歳 )
  <?php endif ?>
</p>

<?php  if(($model->client && ! $model->client->isAdult())): /* CLIENT's PARENT */ ?>
<p style="font-size:12pt">
保護者&nbsp;&nbsp;
<?php if($parent = $model->client->parent): ?>
    <?= $parent->name ?> <?= sprintf('( %d 歳 )', $parent->age) ?>
<?php else: ?>

<?php endif ?>
</p>
<?php elseif ($model->manual_protector_name): ?>
<p style="font-size:12pt">
  保護者&nbsp;&nbsp;<?= $model->manual_protector_name ?>
  <?= ($model->manual_protector_age ? sprintf('( %d 歳 )', $model->manual_protector_age) : '') ?>
</p>
<?php endif ?>

</td>
<td style="text-align:right;font-size:9pt;">

<p>
  発行日&nbsp;
  <?= Yii::$app->formatter->asDate($model->create_date, 'php:Y年 m月 d日 (D)') ?>
</p>

<?php if ($model->center): ?>
<p>
  センター&nbsp;
  <?= $model->center ?: ''?>
</p>
<?php endif ?>

<p>
  ホメオパス&nbsp;
  <?= $model->homoeopath ? $model->homoeopath->homoeopathname : ''?>
</p>
<?php if ($model->tel): ?>
<p>
  電話番号&nbsp;
  <?= $model->tel; ?>
</p>
<?php endif ?>

</td>
</tr>
</table>
<div style="font-size:2pt; padding:0; margin:0;">&nbsp;</div>
<table>
  <tr>
    <th class="remedy_name" width="60%" style="font-size:10pt">品名</th>
    <th class="remedy_info" width="5%"  style="font-size:10pt">数量</th>
    <th class="remedy_info" width="12%" style="font-size:10pt">目安</th>
    <th class="remedy_info" width="7%" style="font-size:10pt">取り方</th>
    <th class="remedy_info" width="15%" style="font-size:10pt">メモ</th>
  </tr>

<?php foreach($model->parentItems as $item): ?>
<?php
    // 取り方の条件
    $take = '';
    switch ($item->vial_id) {
        case RemedyVial::MICRO_BOTTLE:
        case RemedyVial::SMALL_BOTTLE:
        case RemedyVial::MIDDLE_BOTTLE:
        case RemedyVial::LARGE_BOTTLE:
            $take = 'C';
            break;
        case RemedyVial::GLASS_5ML:
        case RemedyVial::ALP_20ML:
            $take = 'A';
            break;
        case RemedyVial::GLASS_20ML:
        case RemedyVial::PLASTIC_SPRAY_20ML:
        case RemedyVial::ALP_100ML:
        case RemedyVial::ORIGINAL_20ML:
        case RemedyVial::ORIGINAL_150ML:
            $take = 'B';
            break;
    }
?>
  <tr>
    <td class="remedy_name">
    <?= $item->fullname ?>
    </td>
    <td class="remedy_info"><?= $item->quantity ?></td>
    <td class="remedy_info"><?= $item->instruction ? $item->instruction->name : null ?></td>
    <td class="remedy_info"><?= $take ?></td>
    <td class="remedy_info"><?= $item->memo ?></td>
  </tr>

<?php endforeach ?>
</table>
<div style="font-size:2pt; padding:0; margin:0;">&nbsp;</div>
<div style="border:3px solid black">
  <table>
  <col width="25%">
  <col width="30%">
  <col width="40%">
  <col width="5%">
  <tr>
    <td>□ 店頭受取</td>
    <td>来店日時&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
　　<td>入荷次第ご連絡&nbsp;&nbsp;□ 要 □ 不要</td>
    <td>&nbsp;</td>
  </tr>
  <tr height="30px">
    <td>店名</td>
    <td>
      <table height="30px">
        <tr>
          <td width="30%"style="vertical-align:bottom;text-align:right">月</td>
          <td width="35%"style="vertical-align:bottom;text-align:right">日</td>
          <td width="35%"style="vertical-align:bottom;text-align:right">時頃</td>
        </tr>
      </table>
    </td>
    <td>
      &nbsp;&nbsp;&nbsp;&nbsp;
      電話番号
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="4">
      <hr>
    </td>
  </tr>
  <tr>
    <td colspan="2" width="90%">
      □宅配受取
    </td>
    <td>□ 時刻指定あり</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">
      &nbsp;&nbsp;
      □ 配達日指定なし<span style="font-size:8pt">（最短日にお届け）</span><br>
      <table>
        <tr>
          <td width="30%"style="vertical-align:top;text-align:left">
            &nbsp;&nbsp;
            □ 配達日指定
          </td>
          <td width="35%"style="vertical-align:bottom;text-align:right">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;月
          </td>
          <td width="35%"style="vertical-align:bottom;text-align:right">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;日
          </td>
        </tr>
      </table>
    </td>
    <td colspan="2">
      &nbsp;&nbsp;
      □午前中
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      □14-16時
      <br>
      &nbsp;&nbsp;
      □16-18時
      &nbsp;&nbsp;
      □18-20時
      &nbsp;&nbsp;
      □19-21時
    </td>
  </tr>
  <tr>
    <td colspan="4" height="2">
      <hr>
    </td>
  </tr>
  <tr>
    <td height="50px">住所 &nbsp;
      〒 <?= $model->delivery ? $model->delivery->zip : null ?>
    </td>
    <td colspan="3">
      <?= $model->delivery ? $model->delivery->addr : null ?>
    </td>
  </tr>
  <tr>
    <td height="50px">氏名</td>
    <td><?= $model->delivery ? $model->delivery->name : ($model->client ? $model->client->name : null) ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="30px">電話番号</td>
    <td><?= $model->delivery ? $model->delivery->tel : ($model->client ? $model->client->tel : null) ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="4" height="2"><hr height="2" size="2px" style="padding:0;background:#000;border-style:solid"></td>
  </tr>
  <tr>
    <td height="100px">[ホメオパス備考欄]</td>
    <td colspan="2" class="homoeo-memo">

      <?= nl2br($model->note) ?>
    </td>
    <td>&nbsp;</td>
  </tr>
</table>
</div>

<p class="notice">
<b>※ご注意&nbsp;</b><br>
1. 有効期限内に1回に限りご注文頂けます。<br>
2. ホメオパスから指示された適用レメディーについてのご質問は、レメディー販売店ではお答えできませんので、ご相談をいただいたホメオパシーセンターの担当ホメオパスにお問い合わせください。<br>
3. ご注文された販売店にて該当のポーテンシーのレメディーがない場合は、近いポーテンシーに変更になる場合もございます。<br>
4. 訂正の入ったレメディー適用書は無効となります。<br>
5. 販売店で直接レメディーを購入される際、適用レメディーに酒類が含まれる場合は、未成年の方へはお売りできません。<br>
6. この適用書は豊受モールならびにレメディー販売店で、お買い求めになれます。<br>
<br>
&copy; <?= date('Y', max(time(), strtotime($model->create_date))) ?> Japanese Homoeopathic Medical Association. All Rights Reserved.
</p>
