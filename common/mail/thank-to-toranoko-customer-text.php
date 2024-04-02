<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/mail/thank-to-toranoko-customer-text.php $
 * $Id: thank-to-toranoko-customer-text.php 1734 2015-11-01 10:36:05Z mori $
 *
 *
 * @var $this yii\web\View
 * @var $branch   \common\models\Branch
 * @var $customer \common\models\Customer
 * @var $invoice \common\models\Purchase or \common\models\Pointing
 */

use \yii\helpers\ArrayHelper;
use \common\models\Pointing;
?>
<?= $customer->name ?> 様

このたびは　とらのこ会　入会・更新の手続きをいただきましてありがとうございます。

<?php if($invoice instanceof Pointing): ?>
つきましては下記のとおり明細をご確認ください。
<?php else: ?>
つきましては下記のとおり年会費(合計<?= Yii::$app->formatter->asCurrency($invoice->total_charge) ?>)を振込くださいますよう、お願い申し上げます。

注文番号 <?= sprintf('%06d', $invoice->purchase_id) ?> 
注文日時 <?= $invoice->create_date ?> 
支払方法 <?= $invoice->payment->name ?> 

振込口座 
------------------------------------------------------------
銀行：ゆうちょ銀行
記号：00180-2
番号：687915
加入者名：ホメオパシーとらのこ会

※他金融機関からゆうちょ銀行口座への振込用口座番号
〇一九（ゼロイチキュウ）店（019）　当座：0687915

※お申込書とお振込みの控えを必ずお手元に保管してくださいませ。
<?php endif ?>

商品明細
------------------------------------------------------------
<?php foreach($invoice->items as $item): ?>
品名 <?= $item->name ?> (<?= $item->code ?>)
単価 <?= Yii::$app->formatter->asCurrency($item->price) ?> 
数量 <?= $item->quantity ?> 

<?php endforeach ?>
商品計   <?= sprintf('%8s', Yii::$app->formatter->asCurrency($invoice->subtotal))      ?> 
消費税   <?= sprintf('%8s', Yii::$app->formatter->asCurrency($invoice->tax))           ?> 
支払合計 <?= Yii::$app->formatter->asCurrency($invoice->total_charge) ?> 

<?= $branch->name ?> 
------------------------------------------------------------
<?= $branch->company->name ?> 
<?= $branch->addr ?> 
<?= $branch->tel ?> 
<?= $branch->email ?> 
------------------------------------------------------------
本メールは手続きが確定した時点で送信される自動配信メールです。このメールに心当たりのない場合は <?= Yii::$app->params['adminEmail'] ?> までご連絡いただけますようお願い申し上げます。
------------------------------------------------------------
