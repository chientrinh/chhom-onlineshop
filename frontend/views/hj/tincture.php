<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\ProductMaster;
use common\models\ProductSubcategory;
use common\models\Subcategory;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/hj/tincture.php $
 * $Id: tincture.php 3888 2018-05-22 08:25:34Z mori $
 *
 * @var $this   yii\web\View
 * @var $model  common\models\Remedy
 */

$company = \common\models\Company::findOne(\common\models\Company::PKEY_HJ);
$this->title = sprintf('%s | %s | %s', $title, $company->name, Yii::$app->name);

if($subc = Subcategory::findOne(['name'=>'マザーチンクチャー']))
    $this->params['breadcrumbs'][] = ['label' => $subc->name, 'url' => ['subcategory','id'=>$subc->subcategory_id] ];

$this->params['breadcrumbs'][] = $title;
$this->params['body_id'] = 'Product';

// prepare images
$slider = [];
$pager  = [];
$images = $model->images;
if(! $model->images)
{
    $img_src  = Url::to('@web/img/default.jpg');
    $slider[] = sprintf('<li><img src="%s" alt="%s" max-width="300"></li>', $img_src, $model->name);
    $pager[]  = sprintf('<span><a data-slide-index="%d" href=""><img src="%s" alt="%s" ></a></span>', count($pager), $img_src, $model->name);
}
else foreach($model->images as $image)
{
    // 画像のean13からRemedyStockを検索しPotency_idを特定する
    $stock = \common\models\RemedyStock::findByBarcode($image->ean13);
    if($stock->potency_id == \common\models\RemedyPotency::MT || $stock->potency_id == common\models\RemedyPotency::JM) {
        $slider[] = sprintf('<li><img src="%s" alt="%s" max-width="300" title="%s"></li>', $image->url, $model->name, $image->caption);
        $pager[]  = sprintf('<span><a data-slide-index="%d" href=""><img src="%s" alt="%s" title="%s"></a></span>', count($pager), $image->url, $model->name, $image->caption);
    }
}

if(Yii::$app->user->isGuest)
    $favorite = false;
else
    $favorite = \common\models\CustomerFavorite::find()->where([
        'customer_id' => Yii::$app->user->id,
        'remedy_id'   => $model->remedy_id,
    ])->exists();

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

$(function(){
    $('html,body').animate({ scrollTop: 0 }, 'fast');
})
";

$this->registerJs($jscode, \yii\web\View::POS_LOAD);
$this->registerJsFile('@web/js/flexslider/jquery.bxslider.js', ['depends'=>['yii\web\YiiAsset','yii\bootstrap\BootstrapAsset']]);

$formatter = new \yii\i18n\Formatter();
$cache_id   = Yii::$app->controller->route;
$duration   = 60 * 60 * 24 * 365; // 365 days
$dependency = new \yii\caching\DbDependency(['sql' => 'SELECT MAX(udate_date) FROM mvtb_product_master']);
?>

<?php if($this->beginCache($cache_id,['duration'=>$duration,'dependency'=>$dependency])): ?>

<div class="col-md-4 product-photo">
  <ul class="bxslider">
    <?= implode('', $slider) ?>
  </ul>
  <div id="bx-pager">
    <?= implode('', $pager) ?>
  </div>
</div>

<div class="col-md-8 product-detail">
  <h3>
    <span class="Shop"><?= $company->name ?></span>
    <span class="Mame"><?= $title ?></span>
  </h3>

  <p><!-- 商品の広告用文言 -->
    <?= $model->advertise ?>
  </p>

  <!-- レメディーごとの広告説明 -->
  <?php foreach($advertisement as $ad): ?>
  <?= "<p>". $formatter->asHtml(nl2br($ad->body)). "</p>" ?>
  <?php endforeach; ?>

  <!-- カテゴリーごとの広告説明 -->
  <?php foreach($category_advertisement as $ad): ?>
  <?= "<p>". $formatter->asHtml(nl2br($ad->body)). "</p>" ?>
  <?php endforeach; ?>

  <p class="Cart">
      <?= $favorite
          ? Html::a('☆お気に入り', ['/profile/favorite/index'], ['class'=>'btn btn-default'])
          : Html::a('お気に入りに追加', ['/profile/favorite/add','rid'=>$model->remedy_id], ['class'=>'btn btn-default']) ?>
  </p>

  <div id="remedy-potencies">
  <?php
  $matrix = [];
  foreach($stocks as $stock)
  {
      $key = $stock->potency_id;

      if(! isset($matrix[$key]))
          $matrix[$key] = [];

      $matrix[$key][] = $stock;
  }
  ?>
<?= \yii\widgets\ListView::widget([
    'dataProvider'  => new \yii\data\ArrayDataProvider([
        'allModels'  => $matrix,
        'sort'       => false,
        'pagination' => false,
    ]),
    'layout'        => '{items}',
    'itemView'      => '/remedy/_form',
]);?>


  </div>

  <?= $this->render('/remedy/_description',['descriptions'=>$descriptions]) ?>

</div>

<div id="otherCategory" class="col-md-12">

    <?php
    $q1 = ProductMaster::find()->where(['remedy_id' => $model->remedy_id]);
    $q2 = ProductSubcategory::find()->where(['ean13'=> $q1->select('ean13') ]);
    $q3 = Subcategory::find()->where(['subcategory_id'=> $q2->select('subcategory_id') ])
        ->orderBy(['parent_id'=>SORT_DESC, 'weight'=>SORT_DESC]);
    ?>

    <div id="Search" class="col-md-3 col-sm-6 col-xs-12">
        <div class="product-search">
            <?= \frontend\widgets\SubcategoryMenu::widget([
                'class'   => 'product-search',
                'title'   => '',
                'company' => $model->company,
                'sub_id'  => $q3->select('subcategory_id')->scalar(),
            ]) ?>
        </div>
    </div>

    <?php if($q2->exists()): ?>
        <?= $this->render('@frontend/views/remedy/_related', ['remedy'=>$model]) ?>
    <?php endif ?>
</div>

<?php endif /* beginCache() */ ?>
