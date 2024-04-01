<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\ProductMaster;
use common\models\ProductSubcategory;
use common\models\Subcategory;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/remedy/view.php $
 * $Id: view.php 3719 2017-11-02 03:16:10Z kawai $
 *
 * $model      ActiveRecord of Remedy
 * $isFavorite bool
 */

if(preg_match("/^FE2/", $model->name))
  $title = $model->abbr;

if(! isset($title))
  $title = $model->abbr .' '. $model->ja;


$company = \common\models\Company::findOne(\common\models\Company::PKEY_HJ);
$this->title = sprintf('%s | %s | %s', $title, $company->name, Yii::$app->name);

if($letter = strtoupper(substr($model->abbr,0,1)))
   $this->params['breadcrumbs'][] = ['label' => $letter, 'url' => [$letter] ];

$this->params['breadcrumbs'][] = $title;
$this->params['body_id'] = 'Product';

$formatter = new \yii\i18n\Formatter();

// prepare images
$slider = [];
$pager  = [];
if(! $images)
{
    // レメディーとフラワーエッセンス共通でこのViewを使用しているため、isRemedyで条件分岐が必要
    if(Yii::$app->request->get('vid') < 5) {
        $img_src  = Url::to('@web/img/default_remedy.jpg');
    } else if(Yii::$app->request->get('vid') == 5) {
        $img_src  = Url::to('@web/img/default_alpo.jpg');
    } else {
        $img_src  = Url::to('@web/img/default.jpg');
    }
    $slider[] = sprintf('<li><img src="%s" alt="%s" max-width="300"></li>', $img_src, $model->name);
    $pager[]  = sprintf('<span><a data-slide-index="%d" href=""><img src="%s" alt="%s" ></a></span>', count($pager), $img_src, $model->name);
}
else foreach($images as $image)
{
    $slider[] = sprintf('<li><a href="%s"><img src="%s" alt="%s" max-width="300" title="%s"></a></li>', Url::to(['view-image','id'=>$model->remedy_id, 'top'=>$image->basename]), $image->url, $model->name, $image->caption);
    $pager[]  = sprintf('<span><a data-slide-index="%d" href=""><img src="%s" alt="%s" title="%s"></a></span>', count($pager), $image->url, $model->name, $image->caption);
}

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

// Javascript to enable link to tab
var url = document.location.toString();
if (url.match('#')) {
    $('.nav-tabs a[href=\"#' + url.split('#')[1] + '\"]').tab('show');
}

";
$this->registerJs($jscode, \yii\web\View::POS_LOAD);
$this->registerJsFile('@web/js/flexslider/jquery.bxslider.js', ['depends'=>['yii\web\YiiAsset','yii\bootstrap\BootstrapAsset']]);

$cache_id   = Yii::$app->controller->route;
$dependency = new \yii\caching\DbDependency(['sql' => 'SELECT MAX(udate_date) FROM mvtb_product_master']);
$variations = [$isFavorite, ];


$matrix = [];
foreach($products as $product)
{
    $key = $product->potency->name;

    if(! isset($matrix[$key]))
        $matrix[$key] = [];

    $matrix[$key][] = $product;
}
$default = in_array('30C', array_keys($matrix)) ? '30C' : array_keys($matrix)[0];
$default = '';
foreach(array_keys($matrix) as $key)
{
    $item = ['label'=>$key, 'url'=>"#{$key}", 'linkOptions'=>['data-toggle'=>'tab']];
    if($default == $key)
        $item['active'] = true;

    $items[] = $item;
}
?>

<?php if($this->beginCache($cache_id,['dependency'=>$dependency,'variations'=>$variations])): ?>
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
    <span class="Name"><?= $title ?></span>
  </h3>
  <p>
    <?= $model->advertise?>
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
      <?= $isFavorite
          ? Html::a('☆お気に入り', ['/profile/favorite/index'], ['class'=>'btn btn-default'])
          : Html::a('お気に入りに追加', ['/profile/favorite/add','rid'=>$model->remedy_id], ['class'=>'btn btn-default']) ?>
  </p>

  <div id="remedy-potencies">

  <?php
  if(1 < count($matrix)):
  ?>
      <?= \yii\bootstrap\Nav::widget([
          'id'      => 'remedy-view-nav',
          'options' => ['class' => 'nav nav-tabs'],
          'items'   => $items,
      ]) ?>

      <div class="tab-content">
          <?php foreach($matrix as $key => $models): ?>
              <div id="<?= $key ?>" class="tab-pane fade in <?= ($default==$key) ? 'active' : null ?>">
                  <?= $this->render('_form',['model'=>$models,'key'=>$key]) ?>
              </div>
          <?php endforeach ?>
      </div>

  <?php else: /* 1 == count($matrix) */?>
      <?= $this->render('_form',['model'=>array_shift($matrix),'key'=>array_shift(array_keys($matrix))]) ?>
  <?php endif ?>

  </div>

  <?= $this->render('_description',['descriptions'=>$descriptions]) ?>

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
        <?= $this->render('_related', ['remedy'=>$model]) ?>
    <?php endif ?>
</div>

<?php endif /* beginCache() */ ?>
