<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/mail/invoice-notify-text.php $
 * $Id: invoice-notify-text.php 1680 2015-10-18 06:53:34Z mori $
 *
 *
 * @var $this yii\web\View
 * @var $model \frontend\models\Invoice
 */

$fmt = Yii::$app->formatter;
?>
<?= $model->customer->name ?> 様

毎度<?= Yii::$app->name ?>をご愛顧いただきまして誠にありがとうございます。
<?= $model->year ?> 年 <?= $model->month ?> 月度 お支払額のご案内を差し上げます。よろしくご査収の上、お手続きいただけますようお願い申し上げます。

ご請求額      <?= str_pad($fmt->asCurrency($model->due_total), 13, ' ', STR_PAD_LEFT) ?> 
------------------------------------------------------------
内訳
商品ご購入    <?= str_pad($fmt->asCurrency($model->due_purchase  ), 13, ' ', STR_PAD_LEFT) ?> 
ポイント還元	<?= str_pad($fmt->asCurrency($model->due_pointing  ), 13, ' ', STR_PAD_LEFT) ?> 
代理店手数料	<?= str_pad($fmt->asCurrency($model->due_commission), 13, ' ', STR_PAD_LEFT) ?> 


なお、詳細は添付PDFファイルにてご確認いただけます。またログイン後、マイページからもご確認できます。

<?= Yii::$app->name ?> 
------------------------------------------------------------
日本豊受自然農株式会社
http://www.toyouke.com
〒419-0107 静岡県田方郡函南町平井1741-61
電話 055-945-0210
------------------------------------------------------------
本メールはお客様のご登録アドレスへ送信しています。このメールに心当たりのない場合は <?= Yii::$app->params['supportEmail'] ?> までご連絡いただけますようお願い申し上げます。
------------------------------------------------------------
