<?php

use yii\helpers\Html;
use \common\models\InventoryStatus;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/inventory/update.php $
 * $Id: update.php 2503 2016-05-12 09:38:58Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Inventory
 */

$inventory_id = sprintf('%06d', $model->inventory_id);
$this->params['breadcrumbs'][] = ['label' => $inventory_id, 'url' => ['view','id'=> $model->inventory_id]];
$this->params['breadcrumbs'][] = ['label' => '編集', 'url' => ['update', 'id'=>$model->inventory_id]];

// 状態を更新しようとしてエラーだった場合、元の istatus_id を表示する
$status = $model->getOldAttribute('istatus_id');
if($status)
    $model->istatus_id = $status;

?>

<div class="inventory-update">

<div class="pull-right">
    <?= Html::a('',['/casher/inventory/batch-update', 'id' => $inventory_id], [
        'class' => 'btn btn-default glyphicon glyphicon-import',
        'title' => 'CSVを読み込む',
    ]) ?>
    <?php if(! $model->isSubmitted()): ?>
    <?= Html::a('', ['refresh','id'=>$inventory_id],[
        'class'=>'btn btn-success glyphicon glyphicon-repeat',
        'title'=>'再計算'
    ]) ?>
    <?php endif ?>
</div>

<h2>&nbsp;</h2>

<?= \yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'attribute' => 'istatus_id',
            'format'    => 'html',
            'value'     => $model->status->name
                         . Html::a('入力完了',['update','id'=>$inventory_id,'status'=>InventoryStatus::PKEY_SUBMIT],['class'=>'btn btn-warning pull-right']),
            'visible'   => $model->hasErrors() || ! $model->isSubmitted(),
        ],
        [
            'attribute' => 'istatus_id',
            'format'    => 'html',
            'value'     => $model->status->name
                         . Html::a('承認する',['update','id'=>$inventory_id,'status'=>InventoryStatus::PKEY_APPROVED],['class'=>'btn btn-danger pull-right']),
            'visible' => (InventoryStatus::PKEY_SUBMIT == $model->istatus_id),
            'visible'   => ! $model->hasErrors() && $model->isSubmitted(),
        ],
        [
            'attribute' => 'create_date',
            'format'    => 'raw',
            'value'     => $this->render('_date', ['model' => $model]),
            'visible'   => ! $model->isSubmitted(),
        ],
        [
            'attribute' => 'create_date',
            'format'    => ['date','php:Y-m-d H:i'],
            'visible'   => $model->isSubmitted(),
        ],
        [
            'attribute' => 'updated_by',
            'value'     => ($u = $model->updator) ? $u->name : null,
        ],
    ],
]) ?>

<?php if($model->hasErrors()): ?>
    <?= Html::errorSummary($model,['class'=>'alert alert-danger']) ?>
<?php endif ?>

<?php \yii\widgets\Pjax::begin(['id' => 'pjax-grid-region']) ?>

<?= $this->render('items-grid',['model'=>$model]) ?>

<?= $this->render('filter-item',['model'=>$model]) ?>

<?= $this->render('search-item',['model'=>$model,'keyword'=>Yii::$app->request->post('keyword')]) ?>

<?php \yii\widgets\Pjax::end(); ?>

</div>
