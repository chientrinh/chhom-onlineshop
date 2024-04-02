<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use common\models\EventVenue;
use common\models\SearchProductFavor;

/**
 * @var $this yii\web\View
 * @var $model  common\models\Product
 * @var $provider yii\data\ActiveDataProvider
 */

$jscode = "
  $(document).ready(function(){
    $('.bxslider').bxSlider({
      infiniteLoop: true,
      hideControlOnEnd: true,
      speed: 500,
      useCSS: false,
      controls: true,
      captions: true
    });
  });
";
$this->registerJs($jscode, \yii\web\View::POS_LOAD);
$this->registerJsFile('@web/js/flexslider/jquery.bxslider.js', ['depends'=>['yii\web\YiiAsset','yii\bootstrap\BootstrapAsset']]);

// prepare images
$slider = [];
$pager  = [];
if(! $model->images)
{
    $img_src  = Url::to('@web/img/default.jpg');
    $slider[] = sprintf('<li><img src="%s" alt="%s" style="max-width:270px"></li>', $img_src, $model->name);
    $pager[]  = sprintf('<span><a data-slide-index="%d" href=""><img src="%s" alt="%s" ></a></span>', count($pager), $img_src, $model->name);
}
else foreach($model->images as $image)
{
    $slider[] = sprintf('<li><a href="%s"><img src="%s" alt="%s" class="btn" style="max-width:270px"></a></li>', Url::to(['/product/view-image','id'=>$model->product_id,'top'=>$image->basename]), $image->url, $model->name);
    $pager[]  = sprintf('<span><a data-slide-index="%d" href=""><img src="%s" alt="%s" ></a></span>', count($pager), $image->url, $model->name);
}

?>

<ul class="bxslider">
    <?= implode('', $slider) ?>
</ul>
<div id="bx-pager">
    <?= implode('', $pager) ?>
</div>
 
