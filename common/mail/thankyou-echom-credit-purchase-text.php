<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/mail/thankyou-purchase-text.php $
 * $Id: thankyou-purchase-text.php 2545 2016-05-26 08:39:17Z naito $
 *
 *
 * @var $this yii\web\View
 * @var $customer \common\models\Customer
 * @var $model \common\models\Purchase
 * @var $sender string represents email
 */
?>
<?= $customer->name ?> 様

このたびは<?= Yii::$app->name ?>をご利用いただきまして誠にありがとうございます。
下記のとおりご注文を承りましたのでご確認のほどお願い申し上げます。

------------------------------------------------------------
※ライブ配信の視聴URLなど大切な情報になりますので、紛失しないようご注意ください
------------------------------------------------------------

注文番号 <?= sprintf('%06d', $model->purchase_id) ?> 
注文日時 <?= $model->create_date ?> 
支払合計 <?= Yii::$app->formatter->asCurrency($model->total_charge) ?> 
支払方法 <?= $model->payment->name ?>

お客様の言葉 <?= $model->customer_msg ? sprintf('「%s」', $model->customer_msg) : null ?> 

商品明細
------------------------------------------------------------
<?php foreach($model->companies as $company): ?>
<?php foreach($model->getItemsOfCompany($company->company_id) as $item): ?>
品名 <?= $item->name ?>
単価 <?= Yii::$app->formatter->asCurrency($item->price + $item->unit_tax) ?> 
数量 <?= $item->quantity ?> 

<?php endforeach ?>
<?php endforeach ?>
[合計： <?= $model->itemCount ?> 点]

------------------------------------------------------------
■　ライブ配信情報
------------------------------------------------------------
<?php if(!$model->customer_id) { ?>
※ライブ視聴URLは、各日程とも共通になります。
<?= 'https://stream.homoeopathy.ac/live/'.$model->purchase_id ?>


<?php } else { ?>
※豊受会員のみなさまへ
CHhomオンラインショップ（マイページ）から視聴が可能です。
https://ec.homoeopathy.ac/profile
　ログイン後、マイページをご確認ください。

<?php } ?>
▼ライブ配信に関するお問い合わせ
E-mail: ec-chhom@homoeopathy.ac

------------------------------------------------------------
カレッジ・オブ・ホリスティック・ホメオパシー（CHhom）
〒158-0096　東京都世田谷区玉川台2-2-3　矢藤第三ビル
TEL：03-5797-3250　FAX：03-5797-3251
E-mail：chhom@homoeopathy.ac

------------------------------------------------------------
本メールはお客様のご購入が確定した時点で送信される自動配信メールです。
このメールに心当たりのない場合は ec-chhom@homoeopathy.ac までご連絡いただけますようお願い申し上げます。
------------------------------------------------------------
