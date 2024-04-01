<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product/sales.php $
 * $Id: sales.php 2126 2016-02-21 00:58:13Z mori $
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
$this->params['breadcrumbs'][] = ['label' => '売上'];

?>

<div class="product-view-sales">

    <p class="pull-right">
        <?php if($model->prev): ?>
        <?= Html::a('', ['view', 'target' => 'sales', 'id' => $model->prev->product_id], ['title' => "前の商品",'class'=>'btn btn-xs btn-default glyphicon glyphicon-chevron-left']) ?>
        <?php endif ?>

        <?php if($model->next): ?>
        <?= Html::a("", ['view', 'target' => 'sales', 'id' => $model->next->product_id], ['title' => "次の商品",'class'=>'btn btn-xs btn-default glyphicon glyphicon-chevron-right']) ?>
        <?php endif ?>
    </p>

    <h1><?= Html::encode($model->name) ?></h1>

    <?= $this->render('_tab',['model'=>$model]) ?>

    <?= GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query'      => $model->getPurchaseItems(),
            'pagination' => [
                'pageParam' => 'page-purchase',
            ],
            'sort'       => [
                'attributes' => [
                    'purchase_id',
                    'quantity',
                    'point_rate',
                    'discount_rate',
                ],
                'defaultOrder' => ['purchase_id'=>SORT_DESC],
            ],
        ]),
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'layout'       => '{items}{summary}{pager}',
        'columns'      => [
            [
                'attribute' => 'purchase_id',
                'format'    => 'html',
                'value'     => function($data){
                    return Html::a(sprintf('%06d',$data->purchase_id), ['/purchase/view','id'=>$data->purchase_id]);
                },
                'headerOptions' => ['class'=>'col-md-2'],
            ],
            [
                'label'     => '拠点',
                'attribute' => 'purchase.branch.name',
                'headerOptions' => ['class'=>'col-md-2'],
            ],
            [
                'attribute' => 'purchase.customer.name',
                'headerOptions' => ['class'=>'col-md-2'],
            ],
            [
                'attribute' => 'quantity',
                'format'    => 'integer',
                'contentOptions' => ['class'=>'text-right'],
            ],
            [
                'attribute' => 'point_rate',
                'format'    => 'integer',
                'contentOptions' => ['class'=>'text-right'],
            ],
            [
                'attribute' => 'discount_rate',
                'format'    => 'integer',
                'contentOptions' => ['class'=>'text-right'],
            ],
        ],
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query'      => $model->getPointingItems(),
            'pagination' => [
                'pageParam' => 'page-pointing',
            ],
            'sort'       => [
                'attributes' => [
                    'pointing_id',
                    'quantity',
                    'point_rate',
                ],
                'defaultOrder' => ['pointing_id'=>SORT_DESC],
            ],
        ]),
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'layout'       => '{items}{summary}{pager}',
        'columns'      => [
            [
                'attribute' => 'pointing_id',
                'format'    => 'html',
                'value'     => function($data){
                    return Html::a(sprintf('%06d',$data->pointing_id), ['/pointing/view','id'=>$data->pointing_id]);
                },
                'headerOptions' => ['class'=>'col-md-2'],
            ],
            [
                'label'     => '販売者',
                'attribute' => 'pointing.seller.name',
                'headerOptions' => ['class'=>'col-md-2'],
            ],
            [
                'attribute' => 'pointing.customer.name',
                'headerOptions' => ['class'=>'col-md-2'],
            ],
            [
                'attribute' => 'quantity',
                'format'    => 'integer',
                'contentOptions' => ['class'=>'text-right'],
            ],
            [
                'attribute' => 'point_rate',
                'format'    => 'integer',
                'contentOptions' => ['class'=>'text-right'],
            ],
        ],
    ]) ?>

</div>
