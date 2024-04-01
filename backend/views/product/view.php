<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\models\ProductMaster;
use common\models\SalesCategory;
use common\models\SalesCategory1;
use common\models\SalesCategory2;
use common\models\SalesCategory3;
use common\models\Company;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product/view.php $
 * $Id: view.php 3044 2016-10-29 03:56:33Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Product
 */


if($company = $model->seller)
    $this->params['breadcrumbs'][] = [
        'label' => $company->name,
        'url' => ['index','company'=>$company->company_id]
    ];

$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view','id'=>$model->product_id]];
if($model->barcode === $model->getBarcode(false))
    $url = Html::a('追加',['jancode/create','id'=>$model->product_id],['class'=>'btn btn-xs btn-warning']);
elseif($model->getProductJan()->exists())
    $url = Html::a('変更',['jancode/update','id'=>$model->product_id],['class'=>'btn btn-xs btn-default']);
elseif($model->getBookinfo()->exists())
    $url = Html::a('変更',['book/update',   'id'=>$model->product_id],['class'=>'btn btn-xs btn-default']);
else
    $url = Html::tag('span','バーコード定義エラー',['class'=>'not-set']);

$master = ProductMaster::findOne(['product_id'=>$model->product_id]);
$salesInfo = SalesCategory::find()->where(['sku_id' => $model->sku_id])->one();
$sales1 = null;
$sales2 = null;
$sales3 = null;

if($salesInfo) {
    $sales1 = $salesInfo->sales1;
    $sales2 = $salesInfo->sales2;
    $sales3 = $salesInfo->sales3;
//var_dump($sales1->bunrui_code1);exit;
}
$sales1data = SalesCategory1::find()->asArray()->all();
$sales2data = SalesCategory2::find()->asArray()->all();
$sales3data = SalesCategory3::find()->asArray()->all();
$salesArray1 = ArrayHelper::getColumn($sales1data, function ($element) {
    return $element['bunrui_code1']." ".$element['name'];
});
$salesArray2 = ArrayHelper::getColumn($sales2data, function ($element) {
    return $element['bunrui_code2']." ".$element['name'];
});
$salesArray3 = ArrayHelper::getColumn($sales3data, function ($element) {
    return $element['bunrui_code3']." ".$element['name'];
});

if($sales1 && $sales2 && $sales3) {
    $bunrui1 = $sales1->bunrui_code1." ".$sales1->name;
    $bunrui2 = $sales2->bunrui_code2." ".$sales2->name;
    $bunrui3 = $sales3->bunrui_code3." ".$sales3->name;
    $company_key = $salesInfo->vender_key;
    if($company_key == 'TR')
        $company_key  = 'trose';

    $vender = $company_key." ".Company::find()->where(['key' => $company_key])->one()->name;
} else {
    $bunrui1 = "";//$salesArray1[0];
    $bunrui2 = "";//$salesArray2[0];
    $bunrui3 = "";//$salesArray3[0];
    $vender = "";//$company->key." ".$company->name;
}

?>

<div class="product-view">

    <p class="pull-right">
        <?= Html::a("編集", ['update', 'id' => $model->product_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <h1>
        <?= Html::encode($model->name) ?>
    </h1>

    <?= $this->render('_tab',['model'=>$model]) ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'category',
                'format'    => 'html',
                'value'     => Html::a($model->category->name, ['/category/view','id'=>$model->category_id]),
            ],
            [
                'attribute' => 'subcategories',
                'format'    => 'html',
                'value'     => \yii\grid\GridView::widget([
                    'dataProvider'=>new \yii\data\ActiveDataProvider(['query'      => $model->getSubcategories(),
                                                                      'pagination' => false,]),
                    'columns' => [
                        [
                            'attribute'=> 'name',
                            'format'   => 'html',
                            'value'    => function($data){ return Html::a($data->fullname, ['subcategory/view','id'=>$data->subcategory_id]); },
                        ],
                    ],
                    'layout'       => '{items}',
                    'tableOptions' => ['class' => 'table-condensed'],
                    'showHeader'   => false,
                    'emptyText'    => '<span class="not-set">未定義です </span>' . Html::a('追加',['update','id'=>$model->product_id,'#'=>'subcategory'],['class'=>'btn btn-xs btn-warning']),
                ]),
            ],
            [
                'attribute' => 'restrict_id',
                'label'     => $model->getAttributeLabel('restrict_id'),
                'value'     => $model->restriction->name,
            ],
            [
                'attribute' => 'code',
                'format'    => 'raw',
                'value'     => Html::tag('code', $model->code),
            ],
            [
                'attribute' => 'barcode',
                'format'    => 'raw',
                'value'     => Html::tag('code', $model->barcode)
                .              Html::a('画像', ['/barcode/view','id'=>$model->barcode],['class'=>'glyphicon glyphicon-barcode','target' => '_blank', 'data-pjax' => 0])
                . '&nbsp;&nbsp;'
                .              Html::a('値札', ['print','type'=>'price-tag','id'=>$model->product_id],['class'=>'glyphicon glyphicon-film','target' => '_blank', 'data-pjax' => 0])
                . Html::tag('span', $url, ['class'=>'pull-right']),
            ],
            [
                'attribute' => 'pickcode',
                'format'    => 'html',
                'value'     => ($pick = $model->pickcode)
                             ? Html::a($pick, ['/product-pickcode/view',  'id'=>$pick])
                             : Html::a('追加', ['/product-pickcode/create','id'=>''],['class'=>'btn btn-xs btn-warning pull-right']),
            ],
            [
                'label'    => '表示名',
                'format'   => 'html',
                'value'    => $master
                            ? $master->name . Html::a('変更',['/product-master/update','id'=>$master->ean13],['class'=>'btn btn-xs btn-default pull-right'])
                            : null,
            ],
            'name',
            'kana',
            [
                'attribute' => 'keywords',
                'format'    => 'raw',
                'value'     => $master ? $master->keywords : ""
            ],
            [
                'attribute' => 'price',
                'format'    => 'raw',
                'value'     => sprintf("&yen;%s", number_format($model->price)),
            ],
            [
                'attribute' => 'tax_id',
                'format'    => 'raw',
                'value'     => \common\models\Tax::findOne($model->tax_id)->name,
            ],
            [
                'attribute' => 'in_stock',
                'format'    => 'raw',
                'value'     => $model->in_stock ? 'OK' : Html::tag('span','NG',['class'=>'btn btn-danger']),
            ],
            [
                'attribute' => 'liquor_flg',
                'format'    => 'raw',
                'value'     => $model->liquor_flg ? 'はい' : 'いいえ'
            ],
            [
                'attribute' => 'upper_limit',
                'format'    => 'raw',
                'value'     => $model->upper_limit
            ],
            [
                'attribute' => 'recommend_flg',
                'format'    => 'raw',
                'value'     => $model->recommend_flg ? '表示する' : '表示しない',
                'visible'   => !$model->restrict_id //「制限なし」の商品以外は表示しない
            ],
            [
                'attribute' => 'recommend_seq',
                'format'    => 'html',
                'visible'   => $model->recommend_flg
            ],
            [
                'attribute' => 'summary',
                'format'    => 'html',
            ],
            [
                'attribute' => 'description',
                'format'    => 'html',
            ],
            'start_date:date',
            [
                'attribute' => 'expire_date',
                'format'    => ['date', 'php:Y-m-d'],
            ],
            [
                //'attribute' => 'vender_key',
                'label'     => '製造元',
                'format'    => 'html',
                'value'     => $vender,
            ],
            [
                //'attribute' => 'bunrui_code1',
                'label'     => '大分類',
                'format'    => 'html',
                'value'     => $bunrui1,
            ],
            [
                //'attribute' => 'bunrui_code2',
                'label'     => '中分類',
                'format'    => 'html',
                'value'     => $bunrui2,
            ],
            [
                //'attribute' => 'bunrui_code3',
                'label'     => '小分類',
                'format'    => 'html',
                'value'     => $bunrui3,
            ],
            [
                //'attribute' => 'sku_id',
                'label'     => 'SKU-ID',
                'format'    => 'html',
                'value'     => $model->getSkuId()
            ],
        ],
    ]) ?>

<?php if($model->isBook()): ?>
<?php if($model->bookinfo): ?>
<div>
<h2><?= Html::a("書誌", ['/book/view', 'id'=>$model->product_id]) ?></h2>
    <?= DetailView::widget([
        'model' => $model->bookinfo,
        'attributes' => [
            'author',
            'translator',
            'page',
            'pub_date',
            'publisher',
            'format.name',
            'isbn',
            [
                //'attribute' => 'vender_key',
                'label'     => '製造元',
                'format'    => 'html',
                'value'     => $vender,
            ],
            [
                //'attribute' => 'bunrui_code1',
                'label'     => '大分類',
                'format'    => 'html',
                'value'     => $bunrui1,
            ],
            [
                //'attribute' => 'bunrui_code2',
                'label'     => '中分類',
                'format'    => 'html',
                'value'     => $bunrui2,
            ],
            [
                //'attribute' => 'bunrui_code3',
                'label'     => '小分類',
                'format'    => 'html',
                'value'     => $bunrui3,
            ],
            [
                //'attribute' => 'sku_id',
                'label'     => 'SKU-ID',
                'format'    => 'html',
                'value'     => $model->getSkuId()
            ],

        ],
    ]) ?>
</div>
<?php else: ?>
<p class="alert alert-warning">この商品は書籍に分類されていますが、書誌が未定義です</p>
<?php endif ?>

<?php endif ?>

<h2>
補足
<!--
<?php if($model->descriptions): ?>
<?= Html::a("補足", ['/product-description/view', 'product_id'=>$model->product_id]) ?>
<?php else: ?>
補足
<?php endif ?>
-->
</h2>

<?php if($model->descriptions): ?>

    <?= GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query'      => $model->getDescriptions(),
            'pagination' => false,
        ]),
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'layout'       => '{items}',
        'columns'      => [
            'title',
            [
                'attribute' => 'body',
                'format'    => 'html',
            ],
        ],
    ]) ?>

<?php endif ?>

<h2>画像</h2>
<div class="row">
<?php foreach($model->images as $image): ?>
  <div class="col-xs-6 col-md-3">
    <a class="thumbnail" href="<?=$image->url?>">
        <?= Html::img($image->url, [
            'alt'=> $image->basename,
            'style'=>'max-width:100px;max-height:100px']) ?>
    </a>
    <small><?= $image->caption ?></small>
  </div>
<?php endforeach ?>
</div>

</div>
