<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/mail/layouts/text.php $
 * $Id: text.php 804 2015-03-19 07:31:58Z mori $
 */
use yii\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */
?>
<?php $this->beginPage() ?>
<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
<?php $this->endPage() ?>
