<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/mail/welcome-to-toranoko-text.php $
 * $Id: welcome-to-toranoko-text.php 1734 2015-11-01 10:36:05Z mori $
 *
 *
 * @var $this     yii\web\View
 * @var $branch   common\models\Branch
 * @var $customer common\models\Customer
 * @var $paid     bool
 */

use \yii\helpers\ArrayHelper;

?>
<?= $customer->name ?> 様

このたびは　とらのこ会　入会の手続きをいただきましてありがとうございます。

<?php if($paid): ?>
<?= Yii::$app->name ?> にてとらのこ会員として特典がご利用いただけます。
<?php else: ?>
<?= Yii::$app->name ?> にてスタンダード会員として特典がご利用できます。
また、入金が確認でき次第、とらのこ会員として特典がご利用いただけます。
<?php endif ?>

これからも末永くお付き合いのほど、どうぞよろしくお願い申し上げます。

<?= $branch->name ?> 
------------------------------------------------------------
<?= $branch->company->name ?> 
<?= $branch->addr ?> 
<?= $branch->tel ?> 
<?= $branch->email ?> 

<?= Yii::$app->name ?> は <?= $branch->company->name ?> の提携サイトです
<?= \yii\helpers\Url::home(true) ?> 
------------------------------------------------------------
日本豊受自然農株式会社
http://www.toyouke.com
〒419-0107 静岡県田方郡函南町平井1741-61
電話 055-945-0210
------------------------------------------------------------
本メールは手続きが確定した時点で送信される自動配信メールです。このメールに心当たりのない場合は <?= Yii::$app->params['adminEmail'] ?> までご連絡いただけますようお願い申し上げます。
------------------------------------------------------------
