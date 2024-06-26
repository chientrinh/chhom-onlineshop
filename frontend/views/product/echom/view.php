<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\ProductMaster;
use common\models\Stock;
use common\models\ProductGrade;
use \common\models\LiveItemInfo;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/product/view.php $
 * $Id: view.php 3045 2016-10-29 04:05:16Z mori $
 */

$master = ProductMaster::findOne(['product_id'=>$model->product_id]);
$this->params['breadcrumbs'] = [];
$this->params['breadcrumbs'][] = ['label' => $model->category->name, 'url' => ['/category/24']];
$this->params['breadcrumbs'][] = ['label' => $model->name];

$liveItemInfo = LiveItemInfo::find()->where(['product_id'=>$model->product_id])->one();
$liveInfo = $liveItemInfo ? $liveItemInfo->info : null;
if(isset($liveInfo) && $liveInfo->companion) {
    $capacity = $liveInfo->capacity;
    $subscription = $liveInfo->subscription;
    $left = $capacity - $subscription;
}

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
    $px   = 300;
    $path = Yii::getAlias("@webroot/assets/images/{$px}/{$image->basename}");
    $image->exportContent($path, $px, false);

    $path = Yii::getAlias("@web/assets/images/{$px}/{$image->basename}");

    $slider[] = sprintf('<li><a href="%s"><img src="%s" alt="%s" class="btn" style="max-width:270px"></a></li>', Url::to(['view-image','id'=>$model->product_id,'top'=>$image->basename]), $path, $model->name);
    $pager[]  = sprintf('<span><a data-slide-index="%d" href=""><img src="%s" alt="%s" ></a></span>', count($pager), $path, $model->name);
}

$formatter = new \yii\i18n\Formatter();

$customer = \common\models\Customer::findOne(Yii::$app->user->id);
// $grade_id = isset(Yii::$app->user->id) ? \common\models\Customer::currentGrade(Yii::$app->user->id) : null;
$grade = ProductGrade::getGrade($model->product_id,$customer);

if($grade) {
    $price = $grade->price;
    $tax = $grade->tax;
} else {
    $price = $model->price;
    $tax = $model->tax;
}
?>

<div class="col-md-4 product-photo">

    <ul class="bxslider">
    <?= implode('', $slider) ?>
    </ul>
    <div id="bx-pager">
    <?= implode('', $pager) ?>
    </div>

</div>
<div class="col-md-8 product-detail">
<br />
    <h3>
        <span class="Mame"><?= $model->name ?></span>
    </h3>
    <p class="Price">価格： <em><?= $formatter->asCurrency($price) ?></em>（税別）
    <?= $formatter->asCurrency($price + $tax) ?> (税込)<br>
    消費税： <?= $formatter->asCurrency($tax) ?><br>

<?php if(isset($liveInfo) && $liveInfo->companion && $liveInfo->companion != "" && isset($left) && $left <= 30): ?>
    <p class="alert alert-danger" >参加定員まであと <em><font size="5"><?= $left ?></font></em>人です</p>
<?php endif ?>


<?php if($pointRate): ?>
    <!-- <span class="text-info">ポイント： <?= $formatter->asInteger($pointRate) ?>%<br></span> -->
<?php endif ?>
<?php if($discountRate): ?>
    <span class="text-info">ご優待： <?= $formatter->asInteger($discountRate) ?>%<br></span>
<?php endif ?>
    </p>

           <p><?= $formatter->asHtml($model->description) ?></p>

           <?php if($model->isLiquor()): ?>
               <p>※この商品はお酒です。20歳以上の年齢であることを確認できない場合には酒類を販売いたしません。</p>
           <?php endif ?>

<?php if((1 == $model->in_stock) && isset($stockQty) && ($stockQty <> 0) && ($stockQty < Stock::ALERT_QTY)): ?>
<!-- <p class="alert alert-danger" >あと <em><font size="5"><?= $stockQty ?></font></em>点 在庫があります</p> -->
<?php endif ?>

<?php if(isset($liveInfo) && $liveInfo->companion && $liveInfo->companion != "" && isset($left) && $left <= 30): ?>
    <p class="alert alert-danger" >参加定員まであと <em><font size="5"><?= $left ?></font></em>人です</p>
<?php endif ?>

<div class="detail-form-content">
<?php if(0 == $model->in_stock): ?>
<p class="Cart">
  <span class="btn alert-danger">完売御礼（入荷次第、再開します）</span>
</p>
<?php else: ?>

    <?php if(2447 == $model->product_id): ?>
        <?= $this->render('_form_multiple',['model'=>$model, 'stockQty' => $stockQty, 'isFavorite' => $isFavorite, 'liveItemInfo' => $liveItemInfo]) ?>
    <?php else: ?>
        <?= $this->render('_form',['model'=>$model, 'stockQty' => $stockQty, 'isFavorite' => $isFavorite, 'liveItemInfo' => $liveItemInfo]) ?>
    <?php endif ?>

<?php endif ?>

</div>


<?php
if($model->descriptions)
  echo \yii\widgets\ListView::begin([
    'dataProvider'  => new \yii\data\ArrayDataProvider([
        'allModels'  => $model->descriptions,
            'pagination' => false,
        ]),
    'itemView'     => function ($model, $key, $index, $widget){return sprintf('<p><strong>%s</strong><br>%s</p><hr>', $model->title, $model->body); },
])->renderItems();
?>

<table class="table table-striped table-bordered">

<?php

if($model->descriptions)
    $items = $model->descriptions;
 else
    $items = [];


// JANコードがセットされている場合は表示する
if($j = $model->productJan)
        // JANコード（45 or 49で始まるコード）は表示するが、内部コード（24 or 25 で始まるコード）は非表示とする。
        if ( substr($j->jan, 0, strlen("4")) == "4")
            $items[] = (object)['title'=>"JANコード", 'body'=>$j->jan];

echo \yii\widgets\ListView::begin([
    'dataProvider'  => new \yii\data\ArrayDataProvider([
        'allModels'  => $items,
            'pagination' => false,
        ]),
    'itemView'     => function ($model, $key, $index, $widget){return sprintf('<tr data-key="%d"><th>%s</th><td>%s</td></tr>', $key, preg_replace('/\(.*\)/','<span class="mini">${0}</span>',$model->title), $model->body); },
])->renderItems();
 ?>

</table>


</div>
