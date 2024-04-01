<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use common\models\ProductMaster;
use common\models\SalesCategory;
use common\models\SalesCategory1;
use common\models\SalesCategory2;
use common\models\SalesCategory3;
use common\models\Company;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy-stock/view.php $
 * $Id: view.php 3044 2016-10-29 03:56:33Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\RemedyStock */

$this->params['breadcrumbs'][] = ['label' => $model->remedy->name, 'url' => ['/remedy/view','id'=>$model->remedy_id]];
$this->params['breadcrumbs'][] = ['label' => '既製レメディー', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name ];

$master = ProductMaster::findOne(['remedy_id'  => $model->remedy_id,
                                  'potency_id' => $model->potency_id,
                                  'vial_id'    => $model->vial_id,
]);

$salesInfo = SalesCategory::find()->where(['sku_id' => $model->sku_id])->one();
$sales1 = $salesInfo->sales1;
$sales2 = $salesInfo->sales2;
$sales3 = $salesInfo->sales3;

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
//var_dump($model->sku_id,$salesInfo,$sales1,$sales2,$sales3);exit;
    $bunrui1 = $sales1->bunrui_code1." ".$sales1->name;
    $bunrui2 = $sales2->bunrui_code2." ".$sales2->name;
    $bunrui3 = $sales3->bunrui_code3." ".$sales3->name;
    $vender = $salesInfo->vender_key." ".Company::find()->where(['key' => $salesInfo->vender_key])->one()->name;
} else {
    $bunrui1 = "";//$salesArray1[0];
    $bunrui2 = "";//$salesArray2[0];
    $bunrui3 = "";//$salesArray3[0];
    $vender = "";//$company->key." ".$company->name;
}

?>
<div class="remedy-stock-view">

    <h1><?= $model->name ?></h1>

    <p class="pull-right">
        <?= Html::a('編集', ['update', 'remedy_id' => $model->remedy_id, 'potency_id' => $model->potency_id, 'vial_id' => $model->vial_id], ['class' => 'btn btn-primary']) ?>
<!--
        <?= Html::a('Delete', ['delete', 'remedy_id' => $model->remedy_id, 'potency_id' => $model->potency_id, 'vial_id' => $model->vial_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
-->
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'category',
                'label'     => 'カテゴリ',
                'format'    => 'html',
                'value'     => $model->category->name,
            ],
            [
                'attribute' => 'subcategories',
                'label'     => 'サブカテゴリー',
                'format'    => 'html',
                'value'     => Html::ul(ArrayHelper::getColumn($model->subcategories,'fullname')),
            ],
            [
                'attribute' => '表示名',
                'format'    => 'html',
                'value'     => $master
                             ? $master->name . Html::a('変更',['/product-master/update','id'=>$master->ean13],['class'=>'btn btn-xs btn-default pull-right'])
                             : null,
            ],
            [
                'attribute' => 'remedy.abbr',
                'format'    => 'html',
                'value'     => Html::a($model->remedy->name, ['/remedy/viewbyname','name'=>$model->remedy->name]),
            ],
            'potency.name',
            'vial.name',
            [
                'attribute' => 'restrict_id',
                'value'     => $model->restriction->name,
            ],
            [
                'attribute' => 'jancode.jan',
                'format'    => 'html',
                'value'     => ($j = $model->jancode)
                             ? Html::a($j->jan, ['jancode/view', 'id' => $j->jan])
                             : Html::a('追加',['jancode/create','id'=>$model->sku_id],['class'=>'btn btn-xs btn-warning pull-right'])
            ],
            [
                'attribute' => 'sku_id',
                'value'     => $model->sku_id,
            ],
            [
                'label'     => $model->getAttributeLabel('pickcode'),
                'attribute' => 'pickcode',
                'format'    => 'html',
                'value'     => ($pick = $model->pickcode) ? Html::a($pick,['/product-pickcode/view','id'=>$pick]) : null,
            ],
            'price:currency',
            [
                'attribute' => 'in_stock',
                'format'    => 'boolean',
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
        ],
    ]) ?>

<small>画像</small>
<div class="row">
<?php foreach($model->images as $image): ?>
  <div class="col-xs-6 col-md-3">
    <a class="thumbnail" href="<?=$image->url?>">
        <?= Html::img($image->url, [
            'alt'  => $image->basename,
            'title'=> $image->caption,
            'style'=> 'max-width:100px;max-height:100px']) ?>
    </a>
  </div>
<?php endforeach ?>
</div>

</div>
