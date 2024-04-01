<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/magazine/views/default/_box.php $
 * $Id: _box.php 2012 2016-01-27 02:11:47Z mori $
 *
 * $model Article
 * $width integer
 */

use yii\helpers\Html;
use yii\helpers\Url;

$commonPath = sprintf('%s/%s/files/common',
                   \Yii::$app->request->baseUrl, 
                   $this->theme->baseUrl);

$imageUrl = Url::toRoute(sprintf('/magazine/%s/%s', $model->dirname, $model->imageTop));
$pageUrl  = $model->url;

if(in_array($model->genre, ['product','astrology']))
{
    $pageUrl = Url::toRoute(sprintf('/magazine/%s', $model->genre));
}
?>

<!-- START OF ITEM -->
<div class="postbox post-<?= $width ?> cat-<?= $model->genre ?>">
<nav>
	<a href="<?= $model->url ?>"><?= $model->genreLabel ?></a>
	<span class="date"><?= date('Y.m.d', $model->date) ?></span>
</nav>
<?= $model->isNew
 ? sprintf('<img class="newicon noshadow fadein" src="%s/img/bot/new.png" alt="New">', $commonPath)
 : null
 ?>
<a class="post" href="<?= $pageUrl ?>">
<span class="thumb"><img src="<?= $imageUrl ?>" class="thumbnail" alt="<?= $imageUrl ?>" title="<?= $model->title ?>" /></span>
<span class="title"><?= $model->title ?></span>
              <span class="excerpt"><?= $model->excerpt ?></span></a>
</div>
<!-- END OF ITEM -->
