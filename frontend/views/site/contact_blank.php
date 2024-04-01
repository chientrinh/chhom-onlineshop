<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/contact_blank.php $
 * $Id: contact_blank.php 1154 2015-07-15 12:53:31Z mori $
 *
 * @var $this yii\web\View
 */

$title = "お問合せ";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id'] = 'Contact';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

return;
?>
