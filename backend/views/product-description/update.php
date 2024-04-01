<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProductDescription */

$this->title = "編集";
$this->params['breadcrumbs'][] = ['label' => "商品", 'url' => ['/product']];
$this->params['breadcrumbs'][] = ['label' => $model->product->name, 'url' => ['product/view','id'=>$model->product_id]];
$this->params['breadcrumbs'][] = ['label' => $model->title];
$this->params['breadcrumbs'][] = "編集";
?>
<div class="product-description-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
