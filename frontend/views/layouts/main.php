<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/layouts/main.php $
 * $Id: main.php 4210 2020-01-06 08:47:14Z mori $
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
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/layouts/main.php $
 * $Id: main.php 4210 2020-01-06 08:47:14Z mori $
 *
 * @var $this \yii\web\View
 * @var $content string
 */

\frontend\assets\AppAsset::register($this);

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

<?php if('Home' == $body_id): ?>
<div id="Visual">
	<ul>
		<li><img src="<?= \yii\helpers\Url::to('@web/img/top_visual01.jpg') ?>" alt="カレンデュラと富士山"></li>
	</ul>
</div>
<?php endif ?>

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
                if($(window).width() <= 320){
                    $('a.navbar-brand').text('豊受　モール');
                } else {
                    $('a.navbar-brand').text(title);
                }

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
            $menuItems[] = [
                'label' => "モバイル会員証",
                'url'   => ['/profile/default/member-card'],
                'linkOptions' => ['class' => 'pc-hide'],
            ];
            $menuItems[] = ['label' => "初めての方へ",   'url' => ['/site/about']];
            $menuItems[] = ['label' => "初めての方へ",   'url' => ['/site/guidance']];
            $menuItems[] = ['label' => "お買い物ガイド",  'url' => ['/site/guide']];
            //$menuItems[] = ['label' => "提携施設",     'url' => ['/facility']];
            if (Yii::$app->user->isGuest) {
                $menuItems[] = ['label' => "会員登録", 'url' => ['/signup']];
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

            $menuItems[] = ['label' => "適用書レメディーの購入", 'url' => ['/recipe/review/index']];
            $menuItems[] = ['label' => "よくある質問", 'url' => ['/site/faq']];
            $menuItems[] = ['label' => "お問合せ",   'url' => ['/site/contact']];
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

<footer class="footer">
	<div class="row Shoplist">
       <div id="w5" class="list-view">
       <h1>～ 出店企業・団体 ～</h1>
       <ul class="slider1">
       <?= \yii\widgets\ListView::begin([
           'dataProvider' => new \yii\data\ActiveDataProvider([
               'query' => \common\models\Company::find()->where(['in','company_id',[1,2,6]]),
           ]),
           'itemView'    => '_company',
           'options'     => ['class'=>'list-view'],
           'itemOptions' => ['tag'=>'li','class'=>'slide'],
       ])->renderItems();?>
       </ul>
       </div>
 	</div><!-- /.row .Shoplist -->

	<div class="container">
        <p class="pull-right">Copyright &copy;<?= date('Y') ?> <a href="http://www.toyouke.com">NIPPON TOYOUKE Natural Farming Co.Ltd</a></p>
		<p class="pull-left"><a href="http://www.toyouke.com/company">会社概要</a></p>
		<p class="pull-left"><a href="<?= Yii::$app->getUrlManager()->createUrl(['site/policy'])?>">プライバシーポリシー</a></p>
		<p class="pull-left"><a href="<?= Yii::$app->getUrlManager()->createUrl(['site/usage'])?>">利用規約</a></p>
		<p class="pull-left Last"><a href="<?= Yii::$app->getUrlManager()->createUrl(['site/legal'])?>">特定商取引に関する法律に基づく表記</a></p>
	</div>
</footer>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
