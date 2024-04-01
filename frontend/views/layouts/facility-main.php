<?php
/**
 * $URL: http://tarax.toyouke.com/svn/MALL/frontend/views/layouts/main.php $
 * $Id: main.php 3323 2017-05-29 05:29:03Z naito $
 *
 * @var $this \yii\web\View
 * @var $content string
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use frontend\widgets\HeaderNavBar;
use yii\widgets\Breadcrumbs;
use frontend\widgets\Alert;

/**
 * $URL: http://tarax.toyouke.com/svn/MALL/frontend/views/layouts/main.php $
 * $Id: main.php 3323 2017-05-29 05:29:03Z naito $
 *
 * @var $this \yii\web\View
 * @var $content string
 */

\frontend\assets\AppAsset::register($this);

$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/jpeg', 'href' => '/logo.jpg']);

if(! isset($this->params['body_id']))
    $body_id = 'Home';
else
    $body_id = $this->params['body_id'];

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-T5F7TX3');</script>
    <!-- End Google Tag Manager -->
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body id="<?= $body_id ?>">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T5F7TX3"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <?php $this->beginBody() ?>
    <div class="wrap">

        <?php
            $jsCode = "

            var searchMain = $('div#main-for-smart');
            // スマホ用ウィジェット
            searchMain.children().hide();

            $('ul#search-menu-for-smart > li').on('click', function(){

                tag  = $(this).attr('id');

                // すでにクリックされていないか
                if($(this).hasClass('active') == false){
                    // タブ表示切り替え
                    $('ul#search-menu-for-smart > li').removeClass('active');
                    $(this).addClass('active');

                    // 一度全部消す
                    searchMain.children().hide();
                    // クリックされた対象物のみ表示
                    searchMain.children('.'+tag).fadeIn(500);
                } else {
                    // タブ表示切り替え
                    $('ul#search-menu-for-smart > li').removeClass('active');
                    searchMain.children().fadeOut(500);
                }
            });

            // PCで表示しているときにはスマホ用検索ウィジェットは制御不可にする
            var form = $('.search-sm-main').children('.form-group').children('.form-control');

            var title = $('a.navbar-brand').text();

            $(window).on('load resize', function(){

                    $('a.navbar-brand').text(title);

                // スマホ用の商品検索ウィジェットの表示幅を大きくし、タイトルの下に表示されるようにする
                var formForsmartPhone = $('body:not(\'#Home\') #product-search-global').parent();
                if ($(window).width() <= 736) {
                    formForsmartPhone.removeClass('col-md-2');
                    formForsmartPhone.addClass('col-md-6');
                } else {
                    formForsmartPhone.removeClass('col-md-6');
                    formForsmartPhone.addClass('col-md-2');
                }

                // スマホ用の商品検索ウィジェットを制御不可にする
                if(screen.width >= 768){
                    form.prop('disabled', true);
                } else {
                    form.prop('disabled', false);
                }
            });";

            $this->registerJs($jsCode);

            // echo '<ul id="w2" class="navbar-nav navbar-toggle nav"><li><span class="sr-only">Toggle navigation</span><a class="btn btn-small btn-warning" href="/index.php/cart" title="いまカートに 0 点あります"><span class="glyphicon glyphicon-shopping-cart"></span>&nbsp;0</a></li></ul>';
            HeaderNavBar::begin([
                'id'         => "site-navbar",
                'brandLabel' => Yii::$app->name,
                'brandUrl'   => Yii::$app->homeUrl,
                'options'    => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            $menuItems = [];
            //$menuItems[] = ['label' => "豊受モール",     'url' => 'https://mall.toyouke.com'];
            //$menuItems[] = ['label' => "お問合せ",   'url' => ['/site/contact']];

            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items'   => $menuItems,
            ]);

            HeaderNavBar::end();
        ?>

        <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>

        <?php if('site/home' === $this->context->route): ?>
        <?= \frontend\widgets\Jumbotron::widget() ?>
        <?php endif ?>

        <?= Alert::widget() ?>

        <?= $content ?>
        </div>
    </div>

<p class="gotop"><a href="#" title="ページトップに戻る">ページトップに戻る</a></p>
<!--
<footer class="footer">
	<div class="container">
        <p class="pull-right">Copyright &copy;<?= date('Y') ?> <a href="http://www.toyouke.com">NIPPON TOYOUKE Natural Farming Co.Ltd</a></p>
		<p class="pull-left"><a href="http://www.toyouke.com/company">会社概要</a></p>
		<p class="pull-left"><a href="<?= Yii::$app->getUrlManager()->createUrl(['site/policy'])?>">プライバシーポリシー</a></p>
		<p class="pull-left"><a href="<?= Yii::$app->getUrlManager()->createUrl(['site/usage'])?>">利用規約</a></p>
		<p class="pull-left Last"><a href="<?= Yii::$app->getUrlManager()->createUrl(['site/legal'])?>">特定商取引に関する法律に基づく表記</a></p>
	</div>
</footer>
-->
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
