<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use \common\models\Company;
use \common\models\SalesCategory;
use \common\models\SalesCategory1;
use \common\models\SalesCategory2;
use \common\models\SalesCategory3;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product-master/update.php $
 * $Id: update.php 3043 2016-10-29 02:17:28Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\ProductMaster
 */

$this->params['breadcrumbs'][] = ['label'=> $model->ean13];

if($model->product)
    $route = ['/product/view', 'id' => $model->product_id];
elseif($s = $model->stock)
    $route = ['/remedy-stock/view',
              'remedy_id' => $s->remedy_id,
              'potency_id'=> $s->potency_id,
              'vial_id'   => $s->vial_id,
    ];
elseif($model->remedy_id)
    $route = ['/remedy/view', 'id' => $model->remedy_id];
else
    $route = null;

$instock = [
    0  => 'いいえ',
    1  => 'はい',
    -1 => '仮想在庫'
];

$query = \common\models\ProductRestriction::find();
$restrictions = \yii\helpers\ArrayHelper::map($query->all(), 'restrict_id', 'name');

$salesInfo = SalesCategory::find()->where(['sku_id' => $model->sku_id])->one();
if($salesInfo) {
    $sales1Info = $salesInfo->sales1;
    $sales2Info = $salesInfo->sales2;
    $sales3Info = $salesInfo->sales3;
    $model->vender_key = Company::find()->where(['key' => $salesInfo->vender_key])->one()->company_id - 1;
    if($sales1Info && $sales2Info && $sales3Info) {
        $model->bunrui_code1 = $sales1Info->bunrui_id -1;
        $model->bunrui_code2 = $sales2Info->bunrui_id -1;
        $model->bunrui_code3 = $sales3Info->bunrui_id -1;
    } else {
        $model->bunrui_code1 = 0;
        $model->bunrui_code2 = 0;
        $model->bunrui_code3 = 0;
    }
} else {
    $model->bunrui_code1 = 0;
    $model->bunrui_code2 = 0;
    $model->bunrui_code3 = 0;
    $model->vender_key = 0;
}

$sales1 = SalesCategory1::find()->asArray()->all();
$sales2 = SalesCategory2::find()->asArray()->all();
$sales3 = SalesCategory3::find()->asArray()->all();
$salesArray1 = ArrayHelper::getColumn($sales1, function ($element) {
    return $element['bunrui_code1']." ".$element['name'];
});
$salesArray2 = ArrayHelper::getColumn($sales2, function ($element) {
    return $element['bunrui_code2']." ".$element['name'];
});
$salesArray3 = ArrayHelper::getColumn($sales3, function ($element) {
    return $element['bunrui_code3']." ".$element['name'];
});

$companies = ArrayHelper::getColumn(Company::find()->asArray()->all(), function ($element) {
    return $element['key']." ".$element['name'];
});

?>
<div id="product-master-update" class="col-md-12">

    <h1><?= $model->ean13 ?></h1>

    <?php $form = \yii\bootstrap\ActiveForm::begin([
        'id'     => 'form-update',
        'layout' => 'default',
        'method' => 'post',
    ]); ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'kana')->textInput() ?>

    <?= $form->field($model, 'keywords')->textInput() ?>

    <?= $form->field($model, 'dsp_priority')->textInput() ?>

    <?= $form->field($model, 'restrict_id')->dropDownList($restrictions) ?>

    <?= $form->field($model, 'vender_key')->dropDownList($companies, ['class'=>'form-control js-input-label']) ?>
    <?= $form->field($model, 'bunrui_code1')->dropDownList($salesArray1, ['class'=>'form-control js-input-label']) ?>
    <?= $form->field($model, 'bunrui_code2')->dropDownList($salesArray2, ['class'=>'form-control js-input-label']) ?>
    <?= $form->field($model, 'bunrui_code3')->dropDownList($salesArray3, ['class'=>'form-control js-input-label']) ?>

    <div class="form-group">
    <?= Html::submitButton('更新',['class'=>'btn btn-primary']) ?>
    </div>

    <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'kana',
            'keywords',
            'price:currency',
            [
                'attribute' => 'restrict_id',
                'value'     => $model->restriction->name,
            ],
            [
                'attribute' => 'in_stock',
                'value'     => ArrayHelper::getValue($instock, $model->in_stock),
            ],
            'update_date:date',
        ],
    ]) ?>

    <div class="text-right">
        <?= Html::a('もっと見る', $route, ['class'=>'btn btn-info'])?>

        <p class="help-block">
        <?= Yii::$app->urlManager->createUrl($route) ?>
        </p>
    </div>

</div>
