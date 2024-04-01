<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/layouts/beta-release.php $
 * $Id: beta-release.php 3312 2017-05-25 11:56:38Z naito $
 *
 * @var $this \yii\web\View
 * @var $content string
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\widgets\Alert;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/layouts/beta-release.php $
 * $Id: beta-release.php 3312 2017-05-25 11:56:38Z naito $
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
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body id="<?= $body_id ?>">
    <?php $this->beginBody() ?>
    <div class="wrap">

<?php if('Home' == $body_id): ?>
<div id="Visual">
	<ul>
		<li><img src="<?= \yii\helpers\Url::to('@web/img/top_visual01.jpg') ?>" alt=""></li>
	</ul>
</div>
<?php endif ?>

        <?php
            NavBar::begin([
                'id'         => "w3",
                'brandLabel' => Yii::$app->name,
                'brandUrl'   => null,
                'options'    => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            $menuItems = [
                ['label' => "初めての方へ",     'url' => ['/site/about']],
                ['label' => "よくある質問",     'url' => ['/site/faq']],
//                ['label' => "WEBマガジン",     'url' => ['/magazine']  ],
            ];
            if (Yii::$app->user->isGuest) {
//                $menuItems[] = ['label' => "会員登録", 'url' => ['/signup']];
                $menuItems[] = ['label' => "ログイン", 'url' => ['/site/login']];
            } else {
                $menuItems[] = ['label' => "マイページ", 'url'=> ['/profile']];
                $menuItems[] = [
                    'label' => "ログアウト",
                    'url'   => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post'],
                ];
            }

            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items'   => $menuItems,
            ]);
            NavBar::end();
        ?>

        <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>

        <?php if(Yii::$app->session->getFlash('success')): ?>
          <div class="alert alert-success"> <?= Yii::$app->session->getFlash('success') ?> </div>
        <?php endif ?>
        <?php if(Yii::$app->session->getFlash('error')): ?>
          <div class="alert alert-danger"> <?= Yii::$app->session->getFlash('error') ?> </div>
        <?php endif ?>

        <?= $content ?>
        </div>
    </div>

<p class="gotop"><a href="#" title="ページトップに戻る">ページトップに戻る</a></p>

<footer class="footer">

	<div class="row Shoplist">

<?php if(0):/* UNDER CONSTRUCTION, NOT FULLY OPEN THE MALL */ ?>
       <div id="w5" class="list-view">
       <h1>～ 出店企業・団体 ～</h1>
       <ul class="slider1">
       <?= \yii\widgets\ListView::begin([
           'dataProvider' => new \yii\data\ActiveDataProvider([
               'query' => \common\models\Company::find()->where(['in','company_id',[1,2,3,4]]),
           ]),
           'itemView'    => '_company',
           'options'     => ['class'=>'list-view'],
           'itemOptions' => ['tag'=>'li','class'=>'slide'],
       ])->renderItems();?>
       <?= \yii\widgets\ListView::begin([
           'dataProvider' => new \yii\data\ActiveDataProvider([
               'query' => \common\models\Company::find()->where(['in','company_id',[1,2,3,4]]),
           ]),
           'itemView'    => '_company',
           'options'     => ['class'=>'list-view'],
           'itemOptions' => ['tag'=>'li','class'=>'slide'],
       ])->renderItems();?>
       </ul>
       </div>
<?php else: ?>
       <div id="w5" class="list-view">
       <h1></h1>
       </div>
<?php endif /* UNDER CONSTRUCTION*/?>

 	</div><!-- /.row .Shoplist -->


	<div class="container">
        <p class="pull-right">Copyright &copy;<?= date('Y') ?> <a href="http://www.toyouke.com">NIPPON TOYOUKE Natural Farming Co.Ltd</a></p>
		<p class="pull-left"><a href="//www.toyouke.com/company">会社概要</a></p>
		<p class="pull-left"><a href="<?= Yii::$app->getUrlManager()->createUrl(['site/policy'])?>">プライバシーポリシー</a></p>
		<p class="pull-left"><a href="<?= Yii::$app->getUrlManager()->createUrl(['site/usage'])?>">利用規約</a></p>
		<p class="pull-left"><a href="<?= Yii::$app->getUrlManager()->createUrl(['site/legal'])?>">特定商取引に関する法律に基づく表記</a></p>

		<p class="pull-left Last"><a href="//www.toyouke.com/contact">お問合せ</a></p>

	</div>
</footer>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
