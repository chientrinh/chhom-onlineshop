<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use \common\models\InventoryStatus;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/inventory/print.php $
 * $Id: print.php 3268 2017-04-21 01:45:07Z kawai $
 *
 * @var $this yii\web\View
 * @var $model common\models\Inventory
 */

$inventory_id = sprintf('%06d', $model->inventory_id);
$this->params['breadcrumbs'][] = ['label' => $inventory_id, 'url' => ['view','id'=>$model->inventory_id]];
$this->params['breadcrumbs'][] = ['label' => '原価集計'];

$csscode = '
@media print {
@page { margin: 0.5cm; }
body {
  font-size: 9pt;
}
td {
  padding:0;
  margin: 0;
}
}
';
$this->registerCss($csscode);

$query = $model->getItems()
               ->join('LEFT JOIN',
                      \common\models\ProductMaster::tableName().' m',
                      'dtb_inventory_item.ean13=m.ean13')
               ->join('LEFT JOIN',
                      \common\models\ProductSubcategory::tableName().' ps',
                      'dtb_inventory_item.ean13=ps.ean13')
               ->join('LEFT JOIN',
                      \common\models\Subcategory::tableName().' s',
                      'ps.subcategory_id=s.subcategory_id and s.subcategory_id not in (11,31)')
               ->join('LEFT JOIN',
                      \common\models\Remedy::tableName().' r',
                      'm.remedy_id=r.remedy_id')
               ->with('product.category',
                      'product.category.seller',
                      'product.category.vendor',
                      'product.subcategories')
               ->orderBy([
                   new \yii\db\Expression('FIELD(s.subcategory_id, 123,32,30,29,28,25,26,27,24,7) DESC'),
                   'm.category_id'  => SORT_ASC,
                   's.parent_id'    => SORT_ASC,
                   's.weight'       => SORT_DESC,
                   'm.potency_id'   => SORT_ASC,
                   'm.vial_id'      => SORT_ASC,
                   'r.abbr'         => SORT_ASC,
                   'm.kana'         => SORT_ASC,
               ]);
?>

<?= \yii\widgets\DetailView::widget([
    'model' => $model,
    'options' => ['class'=>'table table-condensed table-bordered'],
    'attributes' => [
        [
            'attribute' => 'istatus_id',
            'value'     => $model->status->name,
        ],
        [
            'attribute' => 'branch_id',
            'value'     => $model->branch->name,
        ],
        [
            'attribute' => 'create_date',
            'format'    => ['date','php:Y-m-d H:i'],
        ],
        [
            'attribute' => 'update_date',
            'value'     => Yii::$app->formatter->asDate($model->update_date,'php:Y-m-d H:i')
        ],
        [
            'attribute' => 'created_by',
            'value'     => ($c = $model->creator) ? $c->name : null,
        ],
        [
            'attribute' => 'updated_by',
            'value'     => ($u = $model->updator) ? $u->name : null,
        ],
        [
            'attribute' => '合計',
            'format'    => 'html',
            'value'     => Yii::$app->formatter->asInteger($model->getItems()->count())           . " 品目 "
                         . Yii::$app->formatter->asInteger($model->getItems()->sum('actual_qty')) . " 点   ",
        ],
        [
            'label'     => '原価総額',
            'format'    => 'raw',
            'value'     => '&yen;' . number_format($totalCost, 2),
        ],
    ],
]) ?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query'      => $query,
        'pagination' => false,
        'sort'       => false,
    ]),
    'tableOptions'   => ['class'=>'table table-condensed table-striped'],
    'summary'        => sprintf('%d 件中 {begin} から {end} までを表示しています。', $model->getItems()->count()),
    'layout'         => '<div>{summary}</div>{pager}{items}{pager}',
    'columns' => [
        [ 'class' => \yii\grid\SerialColumn::className() ],
        [
            'label' => '製造',
            'attribute' => 'product.category.vendor.key',
            'contentOptions' => ['class' => 'text-uppercase'],
        ],
        [
            'label' => '販社',
            'attribute' => 'product.category.seller.key',
            'contentOptions' => ['class' => 'text-uppercase'],
        ],
        [
           'attribute' => 'product.category.name',
           'contentOptions' => ['class' => 'text-uppercase'],
        ],
        [
            'label' => 'サブカテゴリー',
            'value' => function($data)
            {
                if(!$p = $data->product)
                    return null;

                if($model = $p->getSubcategories()->andWhere(['not in','subcategory_id',[11,31]])->orderBy([
                    new \yii\db\Expression('FIELD(subcategory_id, 123,32,30,29,28,25,26,27,24,7) DESC'),
                    'parent_id'=>SORT_ASC,
                    'weight'   =>SORT_DESC,
                    'subcategory_id' => SORT_ASC,
                ])->one())
                    return $model->name;
            },
        ],
        'kana',
        [
            'attribute' => 'actual_qty',
            'contentOptions' => ['class'=>'text-right','style'=>'font-weight:bold'],
            'headerOptions'  => ['class'=>'text-right'],
        ],
        [
            'attribute' => 'cost.cost',
            'format'    => 'html',
            'value'     => function($data){
                if($c = $data->cost) { return sprintf('%.2f',$c->cost); }
                return '<span class="not-set">未定義</span>';
            },
            'contentOptions' => ['class'=>'text-right'],
        ],
        [
            'label'     => '小計',
            'format'    => 'html',
            'value'     => function($data){
                if($c = $data->cost) { return sprintf('%.2f',$c->cost * $data->actual_qty); }
                return '<span class="not-set">0</span>';
            },
            'contentOptions' => ['class'=>'text-right'],
        ],
    ],
]) ?>
