<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy-potency/view.php $
 * $Id: view.php 1552 2015-09-26 13:25:56Z mori $
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\RemedyPotency */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Remedy Potencies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="remedy-potency-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->potency_id], ['class' => 'btn btn-primary']) ?>
        <!-- ?= Html::a('Delete', ['delete', 'id' => $model->potency_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?-->
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'potency_id',
            'name',
            'weight',
        ],
    ]) ?>

</div>
