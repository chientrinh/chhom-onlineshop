<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL$
 * $Id$
 *
 * @var $this yii\web\View
 * @var $model common\models\ProductPickcode
 */

$this->params['breadcrumbs'][] = ['label' => $model->pickcode];
?>
<div class="product-pickcode-view">

    <p class="pull-right">
        <?= Html::a('編集', ['update', 'id' => $model->pickcode], ['class' => 'btn btn-primary']) ?>
    </p>

    <h1><?= $model->pickcode ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ean13',
            'product_code',
            'pickcode',
            'model.name',
        ],
    ]) ?>

</div>
