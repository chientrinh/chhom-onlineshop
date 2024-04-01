<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/recipe/views/print.php $
 * $Id: print.php 3670 2017-10-13 09:47:41Z naito $
 */
$company = common\models\Company::findOne($company_id);
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
   font-size: 22pt;
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
</style>
<div style="padding:15px;">
    <div style="text-align:center;">
        <h1>精算書</h1>
        <span style="font-weight:bold;font-size:12pt">
          <?= $year . '年' . $month . '月度' ?>
        </span>
    </div>

    <table>
        <tr>
            <td>
                <p style="font-size:4pt">&nbsp;</p>
                <p style="font-size:12pt">
                    <?= $company->name ?> 御中
                </p>
            </td>
            <td style="text-align:right;font-size:9pt;">
                <p>
                  発行日&nbsp;
                  <?= Yii::$app->formatter->asDate(date('Y-m-d'), 'php:Y年 m月 d日') ?>
                </p>
                <p>日本豊受自然農株式会社</p>
            </td>
        </tr>
    <tr>
        <td>
            <h2><u><strong>支払額 <?= '￥' . number_format($sales - $point_given + $point_consume) ?></strong></u></h2>
        </td>
    </tr>
    <br>
    <tr>
        <td>■内訳</td>
    </tr>
    <tr>
        <td>通販売上 <?= '￥' .  number_format($sales) ?></td>
    </tr>
    <tr>
        <td>付与ポイント <?= '￥' .  number_format($point_given) ?></td>
    </tr>
    <tr>
        <td>使用ポイント（店頭） <?= '￥' .  number_format($point_consume) ?></td>
    </tr>
    </table>
</div>