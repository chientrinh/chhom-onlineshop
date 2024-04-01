<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use \common\models\InventoryStatus;
use \common\models\ProductSubcategory;
use \common\models\Subcategory;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/inventory/view.php $
 * $Id: view.php 2503 2016-05-12 09:38:58Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Inventory
 */

$inventory_id = sprintf('%06d', $model->inventory_id);
$this->params['breadcrumbs'][] = ['label' => $inventory_id, 'url'=>['view','id'=>$model->inventory_id]];

$prev = $model->prev;
$next = $model->next;
?>
<div class="inventory-view">

    <p class="pull-right">
        <?= Html::a('',['/casher/inventory/print', 'id' => $inventory_id], [
            'class' => 'btn btn-default glyphicon glyphicon-print',
            'title' => $model->isApproved()?'印刷':'プレビュー',
        ]) ?>
        <?= Html::a('',['/casher/inventory/view', 'id' => $inventory_id, 'format'=>'csv'], [
            'class' => 'btn btn-default glyphicon glyphicon-export',
            'title' => 'CSVを書き出す',
        ]) ?>

        <?= Html::a('編集', ['/casher/inventory/update', 'id' => $model->inventory_id], [
            'class' => 'btn btn-primary' . ($model->isApproved() ? ' disabled' : null),
        ]) ?>

        <?= Html::a( Html::tag('i','',['class'=>'glyphicon glyphicon-chevron-left']),
                     ['view','id'=>$prev ? $prev->primarykey : null],
                     ['class'=>'btn btn-xs btn-default'. ($prev ? null : ' disabled')]) ?>

        <?= Html::a( Html::tag('i','',['class'=>'glyphicon glyphicon-chevron-right']),
                     ['view','id'=>$next ? $next->primaryKey : null],
                     ['class'=>'btn btn-xs btn-default'. ($next ? null : ' disabled')]) ?>
    </p>

    <?php if($model->isApproved()): ?>
        <div class="alert alert-info">
            この棚卸は承認されました（編集不可）
        </div>
    <?php elseif(InventoryStatus::PKEY_INIT == $model->istatus_id): ?>
        <div class="alert alert-warning">
            この棚卸は入力中です
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            この棚卸は、まだ承認されていません
        </div>
    <?php endif ?>

    <?= $this->render('_view',[
        'model'      => $model, 
        'pagination' => new \yii\data\Pagination(),
        'sort'       => new \yii\data\Sort(),
    ]) ?>

</div>
