<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/layouts/_company.php $
 * $Id: _company.php 1150 2015-07-15 03:51:32Z mori $
 */

$baseUrl  = Url::toRoute('/'.$model->key);
$imgSrc   = Url::to(sprintf('@web/img/logo/%s.png', $model->key));
$label    = $model->name;

$threshold = 12;
if($threshold < mb_strlen($label, 'utf8')) // company name is too long
    $label = preg_replace('/(・|株式会社)/', '<br>\\1', $label); // add <br> for cosmetic purpose
?>

<a href="<?= $baseUrl ?>">

<span class="shop-logo">
  <img src="<?= $imgSrc ?>" alt="<?= $model->name ?>">
</span>

<p>
  <?= $label ?>
</p>

</a>
