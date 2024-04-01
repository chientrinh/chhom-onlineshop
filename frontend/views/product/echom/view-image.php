<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/product/view-image.php $
 * $Id: view-image.php 2550 2016-05-27 09:02:42Z mori $
 */

$this->title = sprintf('%s | %s | %s', $model->name, $model->company->name, Yii::$app->name);

$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view','id'=>$model->product_id]];

$this->params['body_id'] = 'Product';

// settings for image slider
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

$formatter = new \yii\i18n\Formatter();

?>

<div>

    <p class="text-right">
        <?= Html::a(Html::tag('button','&times;',['class'=>'close']),['view','id'=>$model->product_id], ['class'=>'btn btn-default help-block','title'=>'商品説明へ戻る']) ?>
    </p>

    <ul id="bxslider">
      <?php foreach($images as $image): ?>
          <?= printf('<li>%s<br><a href="%s"><img src="%s" alt="%s" class="btn" style="max-width:1000px"></a></li>', $image->caption, $image->url, $image->url, $model->name); ?>
      <?php endforeach ?>
    </ul>

    <div class="product-photo" style="width:100%">
        <div id="bx-pager">
            <?php foreach($images as $k => $image): ?>
                <?= sprintf('<span><a data-slide-index="%d" href=""><img src="%s" alt="%s" ></a></span>', $k, $image->url, $image->caption); ?>
            <?php endforeach ?>
        </div>
    </div>

</div>

