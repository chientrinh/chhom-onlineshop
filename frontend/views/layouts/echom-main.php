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

if(! isset($this->params['body_id']))
    $body_id = 'Home';
else
    $body_id = $this->params['body_id'];

$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/jpeg', 'href' => '/logo.jpg']);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
td {
  padding: 0px;
}
@media screen and (max-width: 640px) {
  .tbl-r03 {
    width: 100%;
  }

  .tbl-r03 tr {
    display: block;
    float: left;
    margin: auto;
    vertical-align: middle;  
  }

  .tbl-r03 tr td, 
  .tbl-r03 tr th {
    border-left: none;
    display: block;
    width: 182.5px;
    text-align: center;
    vertical-align: middle;
    
  }

  .tbl-r03 tr td + td {
    border-left: none;
  }

  .tbl-r03 tbody td:last-child {
    border-bottom: solid 1px #ccc;
  }

    </style>
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
            $menuItems[] = ['label' => "初めてのかたへ", 'url'=> ['/site/guide']];
            if (Yii::$app->user->isGuest) {
                $menuItems[] = [
                    'label' => "ログイン",
                    'url' => ['/site/login'],
                    'linkOptions' => ['class' => 'for-smart-468-hide'],
                    ];
            } else {
                $menuItems[] = ['label' => "マイページ", 'url'=> ['/profile']];
            }

            $itemCount = \frontend\modules\cart\Module::getItemCount();
            $menuItems[] = '<li>'
                         . Html::a('カート'
                                   . Html::tag('span',
                                               $itemCount,
                                               ['id'=>'span4-cart-itemCount','class'=>'cart-item for-smart-468-hide'. ($itemCount ? '' : ' cart-empty')]),
                                   ['/cart/default/index'], ['id'=>'link4-cart-index','class'=>'for-smart-468-hide'])
                         . '</li>';
            if (!Yii::$app->user->isGuest) {
                $menuItems[] = [
                    'label' => "ログアウト",
                    'url'   => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post', 'class' => 'for-smart-468-hide'],
                ];
            }
                                     

            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items'   => $menuItems,
            ]);

            HeaderNavBar::end();
        ?>

        <div class="container">
<center>
<!--<h2>今すぐ視聴可能な配信</h2>
<table class="tbl-r03" border=0 width="100%" align="center">
<tr>
<td width="150" height="100" align=center>
<a href="https://stream.homoeopathy.ac/live/pre_congress/"><img src="https://www.homoeopathy.ac/system/wp-content/uploads/2021/07/20210717.jpg" width="80%"></a>
</td>
<td width="150" height="100" align=center>
<a href="https://stream.homoeopathy.ac/live/survive_10_7/"><img src="https://www.homoeopathy.ac/system/wp-content/uploads/2021/07/7.jpg" width="80%"></a>
</td>
<td width="150" height="100" align=center>
<a href="https://stream.homoeopathy.ac/live/mrna/"><img src="https://www.homoeopathy.ac/system/wp-content/uploads/2021/07/korochan210703.jpg" width="80%"></a>
</td>
<td width="150" height="100" align=center>
<a href="https://stream.homoeopathy.ac/live/20210619Eric/"><img src="https://www.homoeopathy.ac/system/wp-content/uploads/2021/06/エリックさんサムネイル.jpg" width="80%"></a>
</td>
<td width="150" height="100" align=center>
<a href="https://stream.homoeopathy.ac/live/HowToCorona/"><img src="https://www.homoeopathy.ac/system/wp-content/uploads/2021/06/howtocorona.jpg" width="80%"></a>
</td>
</tr>

<tr>
<td width="150" height="100" align=center>
<a href="https://stream.homoeopathy.ac/live/korochan_chusya_dame/index_ph2.php"><img src="https://ec.homoeopathy.ac/banner/korochan1.jpg" width="80%"></a>
</td>
<td width="150" height="100" align=center>
<a href="https://stream.homoeopathy.ac/live/homoeopathy_day/"><img src="https://www.homoeopathy.ac/system/wp-content/uploads/2021/04/homoeopathy_day.png" width="80%"></a>
</td>
<td width="150" height="100" align=center>
<a href="https://stream.homoeopathy.ac/live/green_medicine/"><img src="https://www.homoeopathy.ac/system/wp-content/uploads/2021/03/4%E6%9C%8810%E6%97%A5%E3%82%A4%E3%83%99%E3%83%B3%E3%83%88%E7%AC%AC1%E5%BC%BE_760_428_%E3%83%90%E3%83%8A%E3%83%BC-300x169.jpg" width="80%"></a>
</td>
<td width="150" height="100" align=center>
<a href="https://stream.homoeopathy.ac/live/special_live_20210528/"><img src="https://mall.toyouke.com/img/ishikai.jpg" width="80%"></a>
</td>

</tr>
</table>
</div>-->
</center>
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
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
