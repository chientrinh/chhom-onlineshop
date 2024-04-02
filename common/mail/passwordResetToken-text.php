<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/mail/passwordResetToken-text.php $
 * $Id: passwordResetToken-text.php 892 2015-04-16 04:05:51Z mori $
 */

/* @var $this yii\web\View */
/* @var $user common\models\Customer or backend\models\Staff */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/renew-password', 'token' => $token]);
?>
<?= $user->name ?> 様

パスワード初期化の準備ができました。以下のリンクから初期化ページを開き、新しいパスワードを入力してください。
<?= $resetLink ?>

なお、１時間以内に入力がない場合、初期化の準備は取り消されます。

<?= Yii::$app->name ?>

<?= Yii::$app->params['supportEmail'] ?>
