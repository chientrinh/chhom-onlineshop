<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/layouts/main.php $
 * $Id: main.php 3252 2017-04-18 05:44:55Z kawai $
 */
use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="wrap">

        <?php
            NavBar::begin([
                'brandLabel' => Yii::$app->name,
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);

            echo Nav::widget([
                'options'         => ['class' => 'navbar-nav navbar-right'],
                'items'           => [
                    ['label' => "実店舗",  'url' => ['/casher/default/index'], 'visible' => !Yii::$app->user->identity->hasRole(["tenant"])],
                    ['label' => "熱海",   'url' => ['/casher/atami/index'], 'visible' => !Yii::$app->user->identity->hasRole(["tenant"])],
                    ['label' => "六本松",  'url' => ['/casher/ropponmatsu/index'], 'visible' => !Yii::$app->user->identity->hasRole(["tenant"])],
                    ['label' => "商品",   'url' => ['/product/index'], 'visible' => !Yii::$app->user->identity->hasRole(["tenant"])],
                    ['label' => "顧客",   'url' => ['/customer/index'], 'visible' => !Yii::$app->user->identity->hasRole(["tenant"])],
                    ['label' => "売上",   'url' => ['/purchase/index'], 'visible' => !Yii::$app->user->identity->hasRole(["tenant"])],
                    ['label' => "ログイン", 'url' => ['/site/login'],'visible'=> Yii::$app->user->isGuest],
                    ['label' => sprintf("ログアウト (%s)",
                                        Yii::$app->user->isGuest
                                        ? '' 
                                        : preg_replace('/@.+/','',Yii::$app->user->identity->username)),
                     'url' => ['/site/logout'],'visible'=> ! Yii::$app->user->isGuest,
                     'linkOptions' => ['data-method' => 'post'],
                    ],
                ],
                'activateItems'   => true,
                'activateParents' => true,
            ]);

            \backend\widgets\CartNav::widget();

            NavBar::end();
        ?>

        <div class="container">

        <?= Breadcrumbs::widget([
            'homeLink' => ['label'=>'バックヤード','url'=>['/site/index']],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>

        <?= \backend\widgets\Alert::widget() ?>

        <?= $content ?>

        </div>
    </div>

    <footer class="footer">
        <div class="container">
        <p class="pull-left"><?= Html::a("お客様画面", Yii::$app->homeUrl . '/../..') ?> &nbsp; </p>
        <p class="pull-left">&copy; Homoeopathic Education Co Ltd <?= date('Y') ?> &nbsp; </p>
<p class="pull-left"><?= Html::a("サイトマップ",['/site/view','page'=>'sitemap'])?></p>
        <p class="pull-right"> <?= Yii::powered() ?></p>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
