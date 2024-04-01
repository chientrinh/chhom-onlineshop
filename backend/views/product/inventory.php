<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product/inventory.php $
 * $Id: inventory.php 2126 2016-02-21 00:58:13Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Product
 * @var $query InventoryItem::find()
 * @var $allModels[] array
 */

if($company = $model->seller)
    $this->params['breadcrumbs'][] = [
        'label' => $company->name,
        'url' => ['index','company'=>$company->company_id]
    ];

$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view','id'=>$model->product_id]];
$this->params['breadcrumbs'][] = ['label' => '在庫'];

?>

<div class="product-view-inventory">

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
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels'  => $allModels,
            'pagination' => false,
            'sort'       => false,
        ]),
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'layout'       => '{items}',
        'caption'      => date('Y-m-d H:i 現在 (推定)'),
        'columns'      => [
            [
                'label'     => '在庫',
                'attribute' => 'idealQty',
            ],
            [
                'label'     => '拠点',
                'attribute' => 'branch.name',
            ],
        ],
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query'      => $query,
            'pagination' => false,
            'sort'       => false,
        ]),
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'layout'       => '{items}',
        'caption'      => '最近の棚卸',
        'columns'      => [
            [
                'label'     => '在庫',
                'attribute' => 'actual_qty',
            ],
            [
                'label'     => '拠点',
                'attribute' => 'inventory.branch.name',
            ],
            [
                'label'     => '棚卸し年月日',
                'attribute' => 'inventory.create_date',
                'format'    => 'date',
            ],
            [
                'attribute' => 'updated_by',
                'value'     => function($data){ return ($u = $data->updator) ? $u->name : null; },
            ],
            [
                'attribute' => 'inventory.istatus_id',
                'value'     => function($data){
                    if(($i = $data->inventory) && $i->status) return $i->status->name;
                }
            ],
            ['class'=> \yii\grid\ActionColumn::className(),
             'template' => '{view}',
             'urlCreator' => function($action, $model, $key, $index)
                {
                    return ['/inventory/view','id'=>$model->inventory_id];
                }
            ],
        ],
    ]) ?>

</div>
