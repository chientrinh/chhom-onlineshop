<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/magazine/views/default/product.php $
 * $Id: product.php 860 2015-04-08 09:48:54Z mori $
 */

$genre = 'product';

$provider = new \yii\data\ArrayDataProvider([
    'allModels' => $products,
    'sort' => [
        'attributes' => ['fullname',],
    ],
    'pagination' => [
        'pageSize' => 9,
    ],
]);
?>

<div class="the_content_wrap">

<ul class="breadcrumb">
	<li class="home"><a href="<?= \yii\helpers\Url::toRoute('/magazine') ?>">Home</a></li>
	<li><?= $label ?></li>
</ul>
<!-- /breadcrumb -->

<h1 class="title"><span><?= $subtitle ?></span><?= $label ?></h1>

<div id="entry-body">
<div class="entry-content">

<?= \yii\widgets\ListView::widget([
        'dataProvider' => $provider,
        'layout'       => "{pager}\n{items}\n{pager}",
        'itemView'     => '_product',
    ]);
?>

</div>

<br class="clear">

</div><!-- /entry-content -->

</div><!-- /the-conent-wrap -->
