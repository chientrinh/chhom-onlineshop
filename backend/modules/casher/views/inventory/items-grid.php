<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use \common\models\InventoryStatus;
use \common\models\ProductSubcategory;
use \common\models\Subcategory;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/inventory/items-grid.php $
 * $Id: items-grid.php 2293 2016-03-24 03:40:01Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Inventory
 */
$query = $model->getItems()
               ->join('LEFT JOIN',
                      \common\models\ProductMaster::tableName().' m',
                      'dtb_inventory_item.ean13 = m.ean13')
               ->join('LEFT JOIN',
                      \common\models\ProductSubcategory::tableName().' ps',
                      'dtb_inventory_item.ean13 = ps.ean13')
               ->join('LEFT JOIN',
                      \common\models\Subcategory::tableName().' s',
                      'ps.subcategory_id = s.subcategory_id')
               ->join('LEFT JOIN',
                      \common\models\Remedy::tableName().' r',
                      'm.remedy_id = r.remedy_id')
               ->with('product.category',
                      'product.category.seller',
                      'product.category.vendor',
                      'product.subcategories');

if($subcategory_id = Yii::$app->request->get('subcategory_id', 0))
    $query->andWhere(['s.subcategory_id'=>$subcategory_id]);

if($kana = Yii::$app->request->get('kana', null))
    $query->andWhere(['like','dtb_inventory_item.kana',$kana]);

$provider = new \yii\data\ActiveDataProvider([
    'pagination' => ['pageSize' => 10],
    'query'      => $query,
]);

if(! $provider->sort)
     $query->orderBy([
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

<?= \yii\grid\GridView::widget([
    'dataProvider' => $provider,
    'id'           => 'inventory-items-grid',
    'tableOptions' => ['class'=>'table table-condensed table-striped'],
    'summary'      => sprintf('%d 件中 {begin} から {end} までを表示しています。', $model->getItems()->count()),
    'layout'       => '<div>{summary}</div>{pager}{items}{pager}',
    'columns'      => [
        [ 'class' => \yii\grid\SerialColumn::className() ],
        [
            'attribute' => 'ean13',
            'visible'   => ('view' === $this->context->action->id)
        ],
        'kana',
        [
            'attribute' => 'prev_qty',
            'contentOptions' => ['class'=>'text-right text-muted'],
            'headerOptions'  => ['class'=>'text-right'],
        ],
        [
            'attribute' => 'in_qty',
            'contentOptions' => ['class'=>'text-right text-muted'],
            'headerOptions'  => ['class'=>'text-right'],
        ],
        [
            'attribute' => 'out_qty',
            'contentOptions' => ['class'=>'text-right text-muted'],
            'headerOptions'  => ['class'=>'text-right'],
        ],
        [
            'attribute' => 'sold_qty',
            'contentOptions' => ['class'=>'text-right text-muted'],
            'headerOptions'  => ['class'=>'text-right'],
        ],
        [
            'attribute' => 'ideal_qty',
            'contentOptions' => ['class'=>'text-right text-muted'],
            'headerOptions'  => ['class'=>'text-right'],
        ],
        [
            'attribute' => 'actual_qty',
            'format'    => 'raw',
            'value'     => function($data){ return $this->render('_qty',['model'=>$data]); },
            'contentOptions' => ['class'=>'text-right'],
            'headerOptions'  => ['class'=>'text-center'],
        ],
        [
            'attribute' => 'diff_qty',
            'contentOptions' => ['class'=>'text-right'],
            'headerOptions'  => ['class'=>'text-right'],
        ],
        [
            'attribute' => 'updated_by',
            'value'     => function($data){ return ($u = $data->updator) ? $u->name01 : null; },
            'headerOptions'  => ['class'=>'col-xs-2'],
        ],
    ],
]) ?>

