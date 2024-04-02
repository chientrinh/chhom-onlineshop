<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/mail/passwordRenewed-text.php $
 * $Id: passwordRenewed-text.php 943 2015-04-25 01:16:00Z mori $
 */

/* @var $this yii\web\View */
/* @var $user common\models\Customer or backend\models\Staff */

$url = Yii::$app->urlManager->createAbsoluteUrl(['site/login']);
?>
<?= $user->name ?> 様

パスワードの再設定が完了しました。以下のリンクからログインできます。

<?= $url ?>

<?= Yii::$app->name ?>

<?= Yii::$app->params['supportEmail'] ?>
