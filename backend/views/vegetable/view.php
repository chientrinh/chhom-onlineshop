<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use common\models\Vegetable;
use common\models\SalesCategory;
use common\models\SalesCategory1;
use common\models\SalesCategory2;
use common\models\SalesCategory3;
use common\models\Company;
use common\models\ChangeLog;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/vegetable/view.php $
 * $Id: view.php 2933 2016-10-08 02:47:03Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Vegetable
 */

$this->params['breadcrumbs'][] = ['label' => $model->name];

$prev_id = Vegetable::find()->where(['<','veg_id',$model->veg_id])->max('veg_id');
$next_id = Vegetable::find()->where(['>','veg_id',$model->veg_id])->min('veg_id');

$salesInfo = SalesCategory::find()->where(['sku_id' => $model->getSkuId()])->one();
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
    $vender = $salesInfo->vender_key." ".Company::find()->where(['key' => $salesInfo->vender_key])->one()->name;
} else {
    $bunrui1 = "";//$salesArray1[0];
    $bunrui2 = "";//$salesArray2[0];
    $bunrui3 = "";//$salesArray3[0];
    $vender = "";//$company->key." ".$company->name;
}
?>

<div class="vegetable-view">

    <p class="pull-right">
        <?= Html::a('修正', ['update', 'id' => $model->veg_id], ['class' => 'btn btn-primary']) ?>

        <?php if(isset($prev_id)): ?>
            <?= Html::a("", ['view', 'id' => $prev_id], ['title' => "前の野菜",'class'=>'btn btn-xs btn-default glyphicon glyphicon-chevron-left']) ?>
        <?php endif ?>

        <?php if(isset($next_id)): ?>
            <?= Html::a("", ['view', 'id' => $next_id], ['title' => "次の野菜",'class'=>'btn btn-xs btn-default glyphicon glyphicon-chevron-right']) ?>
        <?php endif ?>
    </p>

    <h1><?= $model->name ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'veg_id',
            [
                'attribute' => 'is_other',
                'value'     => $model->is_other == 0 ? '野菜' : 'その他商品',
            ],
            [
                'attribute' => 'division',
                'label'     => '種別',
                'format'    => 'html',
                'value'     => Vegetable::getDivision($model->division),
            ],
            [
                'attribute' => 'origin_area',
                'label'     => '原産地',
                'format'    => 'html',
                'value'     => $model->origin_area,
            ],
            'name',
            'kana',
            [
                'attribute' => 'capacity',
                'label'     => '容量',
                'format'    => 'html',
                'value'     => $model->capacity ? $model->capacity.'g' : null,
            ],
            [
                'attribute' => 'dsp_priority',
                'label'     => '表示順',
                'format'    => 'html',
                'value'     => $model->dsp_priority,
            ],
            [
                'attribute' => 'print_name',
                'label'     => '印刷用名称',
                'format'    => 'html',
                'value'     => $model->print_name,
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
            'create_date',
            'update_date',
        ],
    ]) ?>

    <?= $this->render('_field',['model'=>$model]) ?>

    <div class="col-md-12">
        <h3><small>DB操作履歴</small>
        </h3>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => ChangeLog::find()->where(['tbl'=>$model->tableName(), 'pkey'=>$model->veg_id]),
            'sort'  => ['defaultOrder' => ['create_date' => SORT_DESC]],
        ]),
        'layout'  => '{items}{pager}',
        'showOnEmpty' => false,
        'tableOptions'   => ['class'=>'table table-condensed table-striped'],
        'summaryOptions' => ['class'=>'small text-right pull-right'],
        'columns' => [
            [
                'attribute' => 'create_date',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->create_date,['/change-log/view','id'=>$data->create_date]); }
            ],
            'route',
            'action',
            'user.name',
        ],
        ]); ?>
    </div>

</div>
