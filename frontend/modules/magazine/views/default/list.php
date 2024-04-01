<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/magazine/views/default/list.php $
 * $Id: list.php 2614 2016-06-24 01:03:15Z mori $
 */

if(isset($this->params['genre']))
    $genre = $this->params['genre'];
else
    $genre = 'home';

$provider = new \yii\data\ArrayDataProvider([
    'allModels' => $articles,
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

<?= \yii\widgets\ListView::widget([
        'dataProvider' => $provider,
        'layout'       => "{pager}\n{items}\n{pager}",
        'itemView'     => '_box',
        'viewParams' => ['width' => 300],
    ]);
?>

</div>

<br class="clear">

