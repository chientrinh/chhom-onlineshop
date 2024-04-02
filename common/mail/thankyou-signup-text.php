<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/mail/thankyou-signup-text.php $
 * $Id: thankyou-signup-text.php 3977 2018-08-03 05:18:15Z mori $
 *
 *
 * @var $this yii\web\View
 * @var $customer \common\models\Customer
 */

?>
<?= $customer->name ?> 様

このたびは<?= Yii::$app->name ?>にご登録いただきまして誠にありがとうございます。
下記のとおりご登録を承りましたのでご確認のほどお願い申し上げます。

受付日時 <?= ('NOW()' == $customer->create_date) ? date('Y-m-d H:i') : $customer->create_date ?> 

会員情報
------------------------------------------------------------
[<?= Yii::$app->name ?>] <?= $customer->grade ? $customer->grade->name . "会員" : '' ?> 
<?php if ($customer->code): ?>【会員番号：<?= $customer->code ?>】 <?php endif; ?> 

連絡先
------------------------------------------------------------
お名前   <?= $customer->name ?> 
かな     <?= $customer->kana ?> 
住所     〒<?= $customer->zip ?> <?= $customer->addr ?> 
電話     <?= $customer->tel ?> 

上記の登録内容は「マイページ」にて編集いただけます。
<?= \yii\helpers\Url::to(['/profile/default/view'], true); ?> 



------------------------------------------------------------

日本豊受自然農株式会社
豊受オーガニクスモール事務局

〒158-0096 東京都世田谷区玉川台2-2-3　矢藤第3ビル 2F
TEL: 03-5797-3371　FAX：03-5797-3372
E-mail: member@toyouke.com
URL:    http://toyouke.com

------------------------------------------------------------
本メールはお客様のご登録が確定した時点で送信される自動配信メールです。このメールに心当たりのない場合は <?= Yii::$app->params['supportEmail'] ?> までご連絡いただけますようお願い申し上げます。
------------------------------------------------------------
