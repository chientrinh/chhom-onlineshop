<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/storage/view.php $
 * $Id: view.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Storage */

$this->title = $model->storage_id;
$this->params['breadcrumbs'][] = ['label' => 'Storages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="storage-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->storage_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->storage_id], [
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
            'storage_id',
            'src_id',
            'dst_id',
            'staff_id',
            'ship_date',
            'pick_date',
            'create_date',
            'update_date',
        ],
    ]) ?>

</div>
