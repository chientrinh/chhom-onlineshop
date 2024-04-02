<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/mail/canceled-purchase-text.php $
 * $Id: canceled-purchase-text.php 2384 2016-04-07 04:11:16Z mori $
 *
 *
 * @var $this yii\web\View
 * @var $customer \common\models\Customer
 * @var $model \common\models\Purchase
 * @var $sender string represents email
 */

?>
<?= $customer->name ?> 様

このたびは豊受モールをご利用いただきまして誠にありがとうございます。
下記のご注文のキャンセルを承りましたのでご確認のほどお願い申し上げます。

注文番号 <?= sprintf('%06d', $model->purchase_id) ?> 
注文日時 <?= $model->create_date ?> 
支払合計 <?= Yii::$app->formatter->asCurrency($model->total_charge) ?> 
支払方法 <?= $model->payment->name ?>

備考 <?= $model->note ? sprintf('「%s」', $model->note) : null ?> 

豊受モール
------------------------------------------------------------
日本豊受自然農株式会社
http://www.toyouke.com
〒419-0107 静岡県田方郡函南町平井1741-61
電話 055-945-0210

《STOP未成年者飲酒！ 当モールでは20歳以上の年齢であることを確認できない場合には酒類を販売いたしません》
------------------------------------------------------------
本メールはお客様のご注文が確定した時点で送信される自動配信メールです。このメールに心当たりのない場合は <?= $sender ?> までご連絡いただけますようお願い申し上げます。
------------------------------------------------------------
