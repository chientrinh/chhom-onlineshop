<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product-cost/view.php $
 * $Id: view.php 2307 2016-03-26 08:33:43Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->params['breadcrumbs'][] = $model->name;

?>

<div class="product-cost-view">

    <h1 class="pull-left"><?= Html::encode($model->name) ?></h1>

    <p class="pull-right">
        <?= Html::a('編集', ['update', 'id' => $model->cost_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'cost',
            'ean13',
            'start_date',
            'end_date',
            'created_at',
            'updated_at',
            [
                'attribute' => 'created_by',
                'value'     => $model->creator->name,
            ],
            [
                'attribute' => 'updated_by',
                'value'     => $model->updator->name,
            ],
        ],
    ]) ?>

</div>
