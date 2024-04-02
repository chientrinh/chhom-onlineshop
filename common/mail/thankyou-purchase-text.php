<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/mail/thankyou-purchase-text.php $
 * $Id: thankyou-purchase-text.php 3921 2018-06-06 01:38:25Z mori $
 *
 *
 * @var $this yii\web\View
 * @var $customer \common\models\Customer
 * @var $model \common\models\Purchase
 * @var $sender string represents email
 */

?>
<?= $customer->name ?> 様   <?php if ($customer->code): ?>【会員番号：<?= $customer->code ?>】 <?php endif; ?> 

このたびは<?= Yii::$app->name ?>をご利用いただきまして誠にありがとうございます。
下記のとおりご注文を承りましたのでご確認のほどお願い申し上げます。

注文番号 <?= sprintf('%06d', $model->purchase_id) ?> 
注文日時 <?= $model->create_date ?> 
支払合計 <?= Yii::$app->formatter->asCurrency($model->total_charge) ?> 
支払方法 <?= $model->payment->name ?>

<?php if($model->checkForGift()): ?>
納品書金額表示 <?= ($deliv = $model->delivery) ? $deliv->giftName : '表示'; ?>
<?php endif ?>

<?php if($model->commissions): ?>
代理店手数料 <?= Yii::$app->formatter->asCurrency(array_sum(\yii\helpers\ArrayHelper::getColumn($model->commissions, 'fee', 0))) ?> 
<?php endif ?>
お客様の言葉 <?= $model->customer_msg ? sprintf('「%s」', $model->customer_msg) : null ?> 

商品明細
------------------------------------------------------------
<?php foreach($model->companies as $company): ?>
[出店：<?= $company->name ?>]
<?php foreach($model->getItemsOfCompany($company->company_id) as $item): ?>
品名 <?= $item->name ?> (<?= $item->code ?>)
単価 <?= Yii::$app->formatter->asCurrency($item->price) ?> 
<?php if($item->discount_rate): ?>
ご優待 <?= Yii::$app->formatter->asInteger($item->discount_rate) ?> % 
<?php elseif($item->discount_amount): ?>
ご優待 <?= Yii::$app->formatter->asCurrency($item->discount_amount) ?> 
<?php endif ?>
<?php if($item->point_rate): ?>
ポイント <?= Yii::$app->formatter->asInteger($item->point_rate) ?> % 
<?php elseif($item->point_amount): ?>
ポイント <?= Yii::$app->formatter->asInteger($item->point_amount) ?> 
<?php endif ?>
数量 <?= $item->quantity ?> 

<?php endforeach ?>
<?php endforeach ?>
[合計： <?= $model->itemCount ?> 点]

お支払い明細
------------------------------------------------------------
商品計   <?= sprintf('%8s', Yii::$app->formatter->asCurrency($model->subtotal))      ?> 
消費税   <?= sprintf('%8s', Yii::$app->formatter->asCurrency($model->tax))           ?> 
送料     <?= sprintf('%8s', Yii::$app->formatter->asCurrency($model->postage))        ?> 
手数料   <?= sprintf('%8s', Yii::$app->formatter->asCurrency($model->handling))      ?> 
Pt値引き <?= sprintf('%8s', Yii::$app->formatter->asCurrency(0 - $model->point_consume)) ?> 

ポイント明細
------------------------------------------------------------
今回加算されたポイント <?= sprintf('%8s', number_format($model->point_given)) ?> pt
現在の所持ポイント    <?= sprintf('%8s', $customer->point) ?> pt

お届け先
------------------------------------------------------------
<?php if(! $model->delivery): ?>
(お届け先の指定がありません)

<?php else: ?>
お名前   <?= $model->delivery->name ?> 
住所     〒<?= $model->delivery->zip ?> <?= $model->delivery->addr ?> 
電話     <?= $model->delivery->tel ?> 
配達指定  <?= $model->delivery->dateTimeString ?> 
<?php
     if($model->branch->branch_id == \common\models\Branch::PKEY_ROPPONMATSU) {
         $osechi = false;
         foreach($model->items as $item) {
             if(isset($item->getModel()->product_id) && $item->getModel()->product_id == \common\models\Product::PKEY_OSECHI) {
                 $osechi = true;
                 break;
             }
         }
         if(!$osechi) {
             $date = new \common\models\DeliveryDateTimeForm(['company_id'=> \common\models\Company::PKEY_TY]);
             echo "出荷予定日  ".Yii::$app->formatter->asDate($date->now, 'php:Y年m月d日(D)');
         } else {
             echo "出荷予定日  2017年12月28日(木)";
         }  
     }
?>
<?php endif ?>

------------------------------------------------------------
<?php
//日本豊受自然農株式会社
//http://www.toyouke.com
//〒419-0107 静岡県田方郡函南町平井1741-61
//電話 055-945-0210
// とりあえず会社でやってみた。でもBranchでやることになる。URL以外は取れるはず。
$company = $model->branch->company;
$companyName = '';
$companyAddress = '';
$companyEmail = [];

if ($model->branch->branch_id == \common\models\Branch::PKEY_ROPPONMATSU){
    echo "※豊受自然農の商品は、クール便でのお届けとなります。\n\n";
    $companyName = '(函南物流センター)';
    $companyAddress = sprintf("〒%s %s\n", 
                        $company->zip, 
                        $company->pref->name.
                        $company->addr01.
                        preg_filter('/[^1-9]/', '番地の', $company->addr02)
    );

    $companyEmail[] = sprintf("▼発送に関するお問い合わせ\n email: %s", $model->branch->email);
    $companyEmail[] = sprintf("▼商品に関するお問い合わせ\n email: %s", \Yii::$app->params['supportEmail']);

} else if ($model->branch->branch_id == \common\models\Branch::PKEY_ATAMI) {
    $companyName = '('.$model->branch->name.')';
    $companyAddress = sprintf("〒%s %s\n", $company->zip, $company->addr);

    $companyEmail[] = sprintf("▼発送に関するお問い合わせ\n email: %s", $model->branch->email);
    $companyEmail[] = sprintf("▼商品に関するお問い合わせ\n email: %s\n", \Yii::$app->params['supportEmail']);

} else if ($model->branch->branch_id == \common\models\Branch::PKEY_TROSE) {
    $companyName = '('.$company->name.')';
    $companyAddress = sprintf("〒%s %s\n", $company->zip, $company->addr);

    $companyEmail[] = sprintf("▼商品、発送に関するお問い合わせ\n email: %s", $company->email);
}
    echo "豊受オーガニクスモール". $companyName."\n";
    echo $companyAddress. "\n";
    echo empty($companyEmail) ? "\n" : implode("\n", $companyEmail). "\n";
   

if($company->company_id == \common\models\Company::PKEY_HJ) {
    echo "《STOP未成年者飲酒！ 当モールでは20歳以上の年齢であることを確認できない場合には酒類を販売いたしません》\n";
}
?>
------------------------------------------------------------
本メールはお客様のご注文が確定した時点で送信される自動配信メールです。
このメールに心当たりのない場合は <?= $sender ?> までご連絡いただけますようお願い申し上げます。
------------------------------------------------------------
