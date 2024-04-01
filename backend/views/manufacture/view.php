<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/manufacture/view.php $
 * $Id: view.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Manufacture */

$this->title = $model->manufacture_id;
$this->params['breadcrumbs'][] = ['label' => 'Manufactures', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="manufacture-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->manufacture_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->manufacture_id], [
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
            'manufacture_id',
            'branch_id',
            'staff_id',
            'quantity',
            'craete_date',
        ],
    ]) ?>

</div>
