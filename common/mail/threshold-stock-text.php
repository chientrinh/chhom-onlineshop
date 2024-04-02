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
商品管理　ご担当者 様

下記商品の在庫数が閾値を下回りましたのでご連絡を致します。

商品明細
------------------------------------------------------------
バーコード    <?= $model->ean13 ?> 
商品名      <?= $item->name ?> (<?= $item->code ?>)
現在の在庫数 <?= $model->actual_qty ?> 
閾値        <?= $model->threshold ?>

------------------------------------------------------------
<?php

$company = $model->branch->company;
$companyName = '';
$companyAddress = '';
$companyEmail = [];

if ($model->branch->branch_id == \common\models\Branch::PKEY_ROPPONMATSU){
    $companyName = '(函南物流センター)';
    $companyAddress = sprintf("〒%s %s\n", 
                        $company->zip, 
                        $company->pref->name.
                        $company->addr01.
                        preg_filter('/[^1-9]/', '番地の', $company->addr02)
    );

} else if ($model->branch->branch_id == \common\models\Branch::PKEY_ATAMI) {
    $companyName = '('.$model->branch->name.')';
    $companyAddress = sprintf("〒%s %s\n", $company->zip, $company->addr);

} else if ($model->branch->branch_id == \common\models\Branch::PKEY_TROSE) {
    $companyName = '('.$company->name.')';
    $companyAddress = sprintf("〒%s %s\n", $company->zip, $company->addr);
}
    echo "豊受オーガニクスモール". $companyName."\n";
    echo $companyAddress. "\n";
    echo empty($companyEmail) ? "\n" : implode("\n", $companyEmail). "\n";
?>