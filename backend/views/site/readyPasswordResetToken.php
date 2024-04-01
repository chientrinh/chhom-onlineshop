<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/site/readyPasswordResetToken.php $
 * $Id: readyPasswordResetToken.php 897 2015-04-17 01:24:18Z mori $
 *
 * @var $this yii\web\View
 * @var $form yii\bootstrap\ActiveForm
 * @var $model \frontend\models\PasswordResetRequestForm
 */

$this->title = "パスワード初期化";
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = "準備ができました";
?>
<div class="site-request-password-reset">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
    手順を記載したメールを送信しました。
    メールの内容にしたがってパスワードを初期化してください。
    </p>
</div>
