<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/mail/sodan-canceled-text.php $
 * $Id: sodan-canceled-text.php 1778 2015-11-09 05:04:09Z mori $
 *
 *
 * @var $this     yii\web\View
 * @var $model    common\models\sodan\Room
 */

use \yii\helpers\ArrayHelper;

$fmt = Yii::$app->formatter;

?>
<?= $model->homoeopath->name ?> 様

相談会の予約がキャンセルされました。
拠点 <?= $model->branch->name ?> 
日時 <?= $fmt->asDate($model->itv_date,'php:Y-m-d (D)') ?> <?= $fmt->asTime($model->itv_time,'php:H:i') ?> 
種別 <?= ArrayHelper::getValue($model,'interview.product.name') ?> 
詳細 <?= \yii\helpers\Url::to(['/sodan/room/hpath/view','id'=>$model->room_id], true) ?> 
備考 [<?= $model->note ?>]

送信者　　　　<?= Yii::$app->name ?> 
ホームページ　<?= \yii\helpers\Url::to(Yii::$app->homeUrl, true) ?> 
------------------------------------------------------------
本メールは予約状況が変更された時点で送信される自動配信メールです。このメールに心当たりのない場合は <?= Yii::$app->params['adminEmail'] ?> までご連絡いただけますようお願い申し上げます。
------------------------------------------------------------
