<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\sodan\Interview */

$this->title = $model->itv_id;
$this->params['breadcrumbs'][] = ['label' => 'Interviews', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="interview-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->itv_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->itv_id], [
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
            'itv_id',
            'homoeopath_id',
            'client_id',
            'product_id',
            'status_id',
            'itv_date',
            'create_date',
            'update_date',
            'created_by',
            'updated_by',
            'presence:ntext',
            'impression:ntext',
            'summary:ntext',
            'progress:ntext',
            'advice:ntext',
            'officer_use:ntext',
        ],
    ]) ?>

</div>
