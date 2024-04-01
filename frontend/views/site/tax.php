<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/2019taxguide.php $
 * $Id: about.php 4067 2018-11-28 08:10:14Z kawai $
 *
 * @var $this \yii\web\View
 */

$title = "消費税率改正に関するお知らせ";
$this->title = sprintf($title);
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Tax';
?>
<div class="site-about">

    <h1 class="mainTitle"><?= Html::encode($this->title) ?></h1>
    <p class="mainLead">豊受オーガニクスモールからのご案内</p>

<?=nl2br("
<p>お客様各位</p>
<p>平素より豊受オーガニクスモールをご利用頂き、誠に有難うございます。</p>
<p>2019年10月1日より消費税法改正に伴い、消費税率が8%から10%に変更されます。</p>
<p>これに伴い、当モールでの取扱商品につきましても、</p>
<p><strong><font color=#0000FF>10月1日（火）ご注文受付分</font></strong>より、<strong><font color=#0000FF>新消費税(10%)が適用</font></strong>となります。</p>
<p>なお、健康食品、食品、飲料水は<strong><font color=#FF0000>軽減税率</font></strong>が適用されるため、<strong><font color=#FF0000>従来どおり 8%の消費税率</font></strong>となります。</p>
<p>軽減税率対象の商品につきましては税込価格での変更はございません。</p>
<p>何卒ご理解賜りますようお願い申し上げます。</p>
<p>■消費税率の変更について</p><table border=1><tr align=center><th style='text-align:center;'>ご注文日</th><th style='text-align:center;'>適用税率</th></tr><tr align=center><td width=50%>2019年9月30日（月）まで</td><td width=25%>8%</td></tr><tr align=center><td>2019年10月1日（火）以降</td><td>10%</td></tr></table>
<p>■軽減税率の適用について</p><p>2019年10月1日（火）以降にご注文の飲食料品等（酒類を除く）は、軽減税率（8%）が適用されます。</p>
<p>対象商品は、請求書・納品書等に「※」と表示し、合計額についても税率別の内訳を表示いたします。</p>
")?>
</div>
