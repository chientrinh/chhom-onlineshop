<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/layouts/visitor.php $
 * $Id: visitor.php 2560 2016-06-03 08:18:01Z mori $
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
                'brandLabel' => Yii::$app->name . (Yii::$app->user->identity ? ' <small>出店者</small> ' : ''),
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                    'style' => Yii::$app->user->identity ? 'background-color:#334d00' : null,
                ],
            ]);
            echo Nav::widget([
                'options'         => ['class' => 'navbar-nav navbar-right'],
                'items'           => [
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
        <p class="pull-right"> <?= Yii::powered() ?></p>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
