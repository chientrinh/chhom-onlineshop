<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\ProductMaster;
use common\models\Stock;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/product/view.php $
 * $Id: view.php 3797 2017-12-23 10:17:56Z naito $
 */

$master = ProductMaster::findOne(['product_id'=>$model->product_id]);

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
    <h3>
        <span class="Shop"><?= Html::a($model->company->name,['/'.$model->company->key],['style'=>'color:#999']) ?></span>
        <span class="Mame"><?= $model->name ?></span>
    </h3>
    <p class="Price">価格： <em><?= $formatter->asCurrency($model->price) ?></em>（税別）
    <?= $formatter->asCurrency($model->price + $model->tax) ?> (税込)<br>
    消費税： <?= $formatter->asCurrency($model->tax) ?><br>

<?php if($pointRate): ?>
    <span class="text-info">ポイント： <?= $formatter->asInteger($pointRate) ?>%<br></span>
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
<p class="alert alert-danger" >あと <em><font size="5"><?= $stockQty ?></font></em>点 在庫があります</p>
<?php endif ?>

<?php if(0 == $model->in_stock): ?>
<p class="Cart">
<span class="btn alert-danger">完売御礼（入荷次第、再開します）</span>
</p>
<?php else: ?>

<?= $this->render('_form',['model'=>$model, 'stockQty' => $stockQty, 'isFavorite' => $isFavorite]) ?>

<?php endif ?>

<?php if($model->isBook() && ($info = $model->bookinfo)): ?>
<?= \yii\widgets\DetailView::widget([
    'model'  => $model->bookinfo,
    'options'=> ['class' => 'table table-striped table-bordered'],
    'attributes' => [
        'author',
        [
            'attribute' => 'translator',
            'visible'   => $info->translator,
        ],
        [
            'attribute' => 'page',
            'format'    => 'raw',
            'value'     => number_format($info->page) . 'ページ ' .
                         ($info->actibook
                        ? Html::a("立ち読み", $info->actibook, ['target'=>'_actibook','class'=>'btn btn-xs btn-info'])
                        : null),
        ],
        'publisher',
        [
            'attribute' => 'pub_date',
            'value'     => sprintf("%d 年 %d 月", $info->pubYear, $info->pubMonth),
        ],
        'format.name',
        'isbn',
    ],
]);?>
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

<?php else: /* ! isBook() */ ?>

<table class="table table-striped table-bordered">

<?php

if($model->descriptions)
    $items = $model->descriptions;
 else
    $items = [];

// 商品がトミーローズによる販売であり、商品番号がセットされている場合は表示する
if($model->company->key == "trose" && $model->code)

    $items[] = (object)['title'=>"商品番号", 'body'=>$model->code];

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

<?php endif /* isBook() */?>

<?php /* 豊受の商品のみ、発送についての文言を表示 */
    if($model->company->key == "ty") {
        // おせち専用メッセージ
        if($model->code == '2400000010791') {
 ?>
    <p class="help-block"><?= $date->toOsechiCustomerMessage ?></p>
  <?php } else { ?>
    <p class="help-block"><?= $date->toCustomerMessage ?></p>
  <?php }} ?>
  
</div>

<div id="otherCategory" class="col-md-12">

    <div id="Search" class="col-md-3 col-sm-6 col-xs-12">
        <div class="product-search">
            <?= \frontend\widgets\SubcategoryMenu::widget([
                'class'   => 'product-search',
                'title'   => '',
                'company' => $model->company,
                'sub_id'  => $master->getSubcategories()->orderBy(['parent_id'=>SORT_DESC,'weight'=>SORT_DESC])->scalar(),
            ]) ?>
        </div>
    </div>

    <?php if($master->getSubcategories()->exists()): ?>
        <?= $this->render('_related', ['model'=>$master]) ?>
    <?php endif ?>

</div>
