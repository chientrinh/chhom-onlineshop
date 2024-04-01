<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/remedy/view-image.php $
 * $Id: view-image.php 3335 2017-05-30 10:16:51Z kawai $
 *
 * $model      ActiveRecord of Remedy
 * $isFavorite bool
 */
$title = $model->abbr .' '. $model->ja;
$company = \common\models\Company::findOne(\common\models\Company::PKEY_HJ);
$this->title = sprintf('%s | %s | %s', $title, $company->name, Yii::$app->name);

if($letter = strtoupper(substr($model->abbr,0,1)))
   $this->params['breadcrumbs'][] = ['label' => $letter, 'url' => [$letter] ];

$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['viewbyname','name'=>$model->abbr]];
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
});
";
$this->registerJs($jscode, \yii\web\View::POS_LOAD);
$this->registerJsFile('@web/js/flexslider/jquery.bxslider.js', ['depends'=>['yii\web\YiiAsset','yii\bootstrap\BootstrapAsset']]);

?>

<div class="col-md-12">

  <p class="text-right">
      <?= Html::a(Html::tag('button','&times;',['class'=>'close']),['viewbyname','name'=>$model->name], ['class'=>'btn btn-default help-block','title'=>'商品説明へ戻る']) ?>
  </p>

  <ul id="bxslider">
      <?php foreach($images as $image): ?>
          <?= sprintf('<li>%s<br><a href="%s"><img src="%s" alt="%s" class="btn" style="max-width:1000px"></a></li>', $image->caption, $image->url, $image->url, $model->name); ?>
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
