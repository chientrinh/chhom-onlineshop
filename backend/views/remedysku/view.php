<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedysku/view.php $
 * $Id: view.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\RemedySku */

$this->title = $model->sku_id;
$this->params['breadcrumbs'][] = ['label' => 'Remedy Skus', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="remedy-sku-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->sku_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->sku_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'sku_id',
            'series.name',
            'vial.name',
            'price',
            [
                'attribute' => 'start_date',
                'format' => 'date',
            ],
            [
                'attribute' => 'expire_date',
                'format' => 'date',
            ],
        ],
    ]) ?>

</div>
