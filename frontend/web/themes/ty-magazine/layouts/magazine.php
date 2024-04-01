<?php

use yii\base\InvalidParamException;
use \yii\helpers\Url;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/web/themes/ty-magazine/layouts/magazine.php $
 * $Id: magazine.php 902 2015-04-17 06:24:43Z mori $
 *
 * created by ooishi@tak-zone.net
 * edited  by mori@homoeopathy.co.jp on 2015.03.26
 *
 * @var $this \yii\web\View
 * @var $content string
 */
// You can use the registerAssetBundle function if you'd like
//$this->registerAssetBundle('app');

$commonPath = sprintf('%s/%s/files/common',
                   \Yii::$app->request->baseUrl, 
                   $this->theme->baseUrl);

if(! isset($this->params['commonPath']))
    $this->params['commonPath'] = $commonPath;

if(isset($this->params['genre']))
    $genre = $this->params['genre'];
else
    $genre = 'home';

// $genre should be one of
// ['home', 'interview', 'farm', 'botanical', 'essay', 'recipe', 'astrology', 'product'];
// however we don't verify it here, as theme might be updated in the future.

?>
<?= $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8" />
<!--[if lt IE 9]>
<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<title><?= $this->title ? $this->title . ' | ' : null ?>豊受ウェブマガジン | Toyouke Web Magazine</title>

<meta name="description" content="" />
<meta name="keywords"  content="" />

<meta name="copyright" content="&copy; 2015 NIPPON TOYOUKE Natural Farming Co.Ltd">
<meta name="robots" content="index, follow, noarchive">
<link rel='stylesheet' id='pii-style-css'  href='<?= $commonPath ?>/css/style.css' type='text/css' media='all' />
<link rel='stylesheet' id='pii-slyder-css'  href='<?= $commonPath ?>/css/flexslider.css' type='text/css' media='all' />
<!--[if lt IE 9]>
<link rel='stylesheet' id='pii-ie-css'  href='<?= $commonPath ?>/css/style-ie.css' type='text/css' media='all' />
<![endif]-->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type='text/javascript' src='<?= $commonPath ?>/js/google.analytics.js'></script>
<?= $this->head(); ?>
</head>

<body class="<?= $genre ?>">

<?= $this->beginBody(); ?>
<div id="container">
<section>
<header id="branding">
<h1 class="site-title"><a href="<?= \yii\helpers\Url::toRoute('/magazine') ?>">豊受ウェブマガジン</a></h1>
<nav class="navigation">
	<div class="menu-header-container">
		<ul class="menu">
			<li id="menu-item-01" class="menu-item"><a href="<?= Url::to(['/magazine/interview']) ?>">Special Interview</a></li>
			<li id="menu-item-02" class="menu-item"><a href="<?= Url::to(['/magazine/farm'])      ?>">農場通信</a></li>
			<li id="menu-item-03" class="menu-item"><a href="<?= Url::to(['/magazine/botanical']) ?>">植物図鑑</a></li>
			<li id="menu-item-04" class="menu-item"><a href="<?= Url::to(['/magazine/essay'])     ?>">エッセイ</a></li>
			<li id="menu-item-05" class="menu-item"><a href="<?= Url::to(['/magazine/recipe'])    ?>">豊受食堂レシピ</a></li>
			<li id="menu-item-06" class="menu-item"><a href="<?= Url::to(['/magazine/astrology']) ?>">12星座占い</a></li>
			<li id="menu-item-07" class="menu-item"><a href="<?= Url::to(['/magazine/product'])   ?>">新商品情報</a></li>
			<li id="menu-item-00" class="menu-item"><a href="<?= Url::to(['/magazine'])           ?>">ホーム</a></li>
		</ul>
	</div>
</nav>
<div class="corp-logo"><a href="http://toyouke.com/" target="_blank">農業生産法人 日本豊受自然農株式会社</a></div>
<!-- <div class="btn-login"><a href="<?= Url::to(['/magazine/login'])?>"><span>ログイン</span></a></div> -->
</header>
</section>

<section>
<article>

<?= $content ?>

<br class="clear">

</article>
</section>

<p class="gotop">
	<a href="#" title="ページトップに戻る">ページトップに戻る</a>
</p>

<section id="footer" class="information">
	<article>
		<ul>
			<li><a href="http://toyouke.com/wordpress/library/" target="_blank"><img src="<?= $commonPath ?>/img/banner/bn_library.jpg" /></a></li>
			<li><a href="http://toyouke.com/catalog/" target="_blank"><img src="<?= $commonPath ?>/img/banner/bn_catalog.jpg" /></a></li>
			<li><a href="http://ec.toyouke.com/" target="_blank"><img src="<?= $commonPath ?>/img/banner/online1.jpg" /></a></li>
			<li><a href="http://shop.homoeopathy.ac/chhom/" target="_blank"><img src="<?= $commonPath ?>/img/banner/bn_chhom.jpg"></a></li>
		</ul>
	</article>
</section>
<!-- /information -->

<footer class="clearfix">
	<div class="footer_inner">
		<nav class="clearfix">
		<ul class="nav">
			<li class="menu-item"><a href="http://toyouke.com/company">会社概要</a></li>
			<li class="menu-item Last"><a href="http://toyouke.com/privacy">プライバシーポリシー</a></li>
		</ul>
	</nav>
	<div class="copyright">&copy; 2015 NIPPON TOYOUKE Natural Farming Co.Ltd</div>
</div>
</footer>

</div>
<!-- /container -->

<script type='text/javascript' src='<?= $commonPath ?>/js/function.js'></script>
<script type='text/javascript' src='<?= $commonPath ?>/js/lazy-load/jquery.sonar.min.js'></script>
<script type='text/javascript' src='<?= $commonPath ?>/js/lazy-load/lazy-load.js'></script>
<script type='text/javascript' src='<?= $commonPath ?>/js/flexslider/jquery.flexslider-min.js'></script>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$(document).bind("contextmenu",function(e){ return false;});
	$("article img").mousedown(function(){return false;});
		$('.flexslider').flexslider({
		animation: 'fade',
		controlNav: false
	});
});
</script>

</body>
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
