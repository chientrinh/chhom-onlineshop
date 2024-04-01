<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/magazine/views/default/_product.php $
 * $Id: _product.php 1282 2015-08-13 04:34:56Z mori $
 *
 * $model Article
 */

use yii\helpers\Url;
use yii\helpers\Html;

$cssdata = "
div.excerpt {
height:7em;
}
";
$this->registerCss($cssdata);

$commonPath = sprintf('%s/%s/files/common',
                   \Yii::$app->request->baseUrl, 
                   $this->theme->baseUrl);

$imageUrl = Url::toRoute(sprintf('/magazine/%s/%s', $model->dirname, $model->imageTop));

?>

<!-- START OF ITEM -->

<div class="postbox post-300 cat-<?= $model->genre ?>">
<nav>
	<span class="date"><?= date('Y.m.d', $model->date) ?></span>
</nav>

<?php if($model->isNew): ?>
<img class="newicon noshadow fadein" src="<?= $commonPath ?>/img/bot/new.png" alt="New">
<?php endif ?>

<a class="post" href="<?= $model->url ?>">
  <span class="thumb"><img src="<?= $imageUrl ?>" class="thumbnail" alt="<?= $model->title ?>" title="<?= $model->title ?>" /></span>
  <span class="title"><?= $model->title ?></span>
  <div class="excerpt"><span class="excerpt"><?= $model->summary ?></span></div>
  <span class="buy">購入する</span>
</a>

</div>
<!-- END OF ITEM -->
