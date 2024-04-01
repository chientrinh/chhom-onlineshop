<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/delivtime/view.php $
 * $Id: view.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\DelivTime */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Deliv Times', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="deliv-time-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->dtime_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->dtime_id], [
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
            'dtime_id:datetime',
            'deliveror_id',
            'time',
            'name',
        ],
    ]) ?>

</div>
