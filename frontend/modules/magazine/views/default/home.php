<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/magazine/views/default/home.php $
 * $Id: home.php 2012 2016-01-27 02:11:47Z mori $
 *
 * $topImage array of string (basename of image src)
 * $feature array of Article
 * $regular array of Article
 */
$provider01 = new \yii\data\ArrayDataProvider([
    'allModels' => $feature,
    'sort' => [
        'attributes' => ['fullname',],
    ],
    'pagination' => [
        'pageSize' => 3,
    ],
]);

$provider02 = new \yii\data\ArrayDataProvider([
    'allModels' => $regular,
    'sort' => [
        'attributes' => ['fullname',],
    ],
    'pagination' => [
        'pageSize' => 8,
    ],
]);
?>

<figure>
<div class="flexslider">
<ul class="slides">
<?php
foreach($topImage as $image)
{
    echo sprintf(
        '<li><a href="%s"><img src="%s" /></a></li>',
        $image->article->url,
        $image->url
    ), "\n";
}
?>
</ul>
</div>
</figure>

<div class="feature">

<h3 class="title"><span>Feature</span></h3>

<?= \yii\widgets\ListView::widget([
        'dataProvider' => $provider01,
        'layout'       => "{items}",
        'itemView'     => '_box',
        'viewParams'   => ['width'=>300],
    ]);
?>

</div>
<!-- /feature --> 

<br class="clear">

<div class="regular">

<h3 class="title"><span>Regular</span></h3>

<?= \yii\widgets\ListView::widget([
        'dataProvider' => $provider02,
        'layout'       => "{items}",
        'itemView'     => '_box',
        'viewParams'   => ['width'=>220],
    ]);
?>

</div>
<!-- /regular -->
