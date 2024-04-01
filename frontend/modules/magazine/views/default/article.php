<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/magazine/views/default/article.php $
 * $Id: article.php 2613 2016-06-24 00:58:29Z mori $
 *
 *
 * $model app\modules\magazine\models\Article
 * $truncate bool
 */

$pageTitle = sprintf("Vol. %d %s", $model->vol, $model->title);

?>

<div class="the_content_wrap">

<ul class="breadcrumb">
	<li class="home"><a href="<?= \yii\helpers\Url::toRoute('/magazine') ?>">Home</a></li>
	<li><a href="<?= \yii\helpers\Url::toRoute(sprintf('/magazine/%s',$model->genre)) ?>"><?= $model->genreLabel ?></a></li>
	<li><?= $pageTitle ?></li>
</ul>
<!-- /breadcrumb -->

<div id="entry-body">

<div class="entry-number"><?= sprintf('%02d', $model->vol) ?></div>
<div class="entry-title">
	<h1><?= $model->title ?></h1>
    <p><?= $model->subtitle ?></p>
</div><!-- entry-title -->

<div class="photo_columnsx2">
	<ul>
<?php
if(isset($model->images[0]))
    echo sprintf('<li class="ph-main"><img src="%s" alt="%s"></li>', $model->images[0], $pageTitle);

if(isset($model->images[1]) && ('astrology' != $model->genre))
    echo sprintf('<li><img src="%s" alt="%s"></li>', $model->images[1], $pageTitle);

if(isset($model->images[2]) && ('astrology' != $model->genre))
    echo sprintf('<li><img src="%s" alt="%s"></li>', $model->images[2], $pageTitle);
?>
	</ul>
</div><!-- /photo_columnsx2 -->

<div class="entry-content">
    <?php if(isset($truncate) && $truncate) : ?>

    <?= $this->context->module->truncateText($model->html) ?>
    <?= $this->render('_sorry') ?>

    <?php else: ?>

    <?= $model->html ?>

    <?php endif ?>

</div>

<nav id="nav-below" class="clearfix">
<?php
	if($model->prev)
    {
        $article = $model->prev;
        echo sprintf('<div class="nav-next"><a href="%s" title="Vol.%d - %s"></a></div>',
                     $article->url,
                     $article->vol,
                     $article->title);
    }
	if($model->next)
    {
        $article = $model->next;
        echo sprintf('<div class="nav-next"><a href="%s" title="Vol.%d - %s"></a></div>',
                     $article->url,
                     $article->vol,
                     $article->title);
    }
?>
</nav>

</div><!-- /entry-body -->

<?php
    if('astrology' == $model->genre)
    {
        return;
    }
?>
<div id="recent-entries">

<h3 class="title"><?= $model->genreLabel ?><span>&lt;最近の記事&gt;</span></h3>

<?php
$provider = new \yii\data\ArrayDataProvider([
    'allModels' => $model->recents,
    'sort' => [
        'attributes' => ['fullname',],
    ],
    'pagination' => false,
]);

echo \yii\widgets\ListView::widget([
    'dataProvider' => $provider,
    'layout'       => "{items}",
    'itemView'     => '_box',
    'viewParams'   => ['width' => 220],
]);
?>

<nav id="nav-below" class="clearfix">
    <div class="nav-prev">&nbsp;</div>
    <div class="nav-next"><a href="<?= \yii\helpers\Url::toRoute(sprintf('/magazine/%s', $model->genre)) ?>" title="全てを見る">全てを見る</a></div>
</nav>

</div><!-- /recent-entries -->

