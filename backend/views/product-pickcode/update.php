<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProductPickcode */

$this->params['breadcrumbs'][] = ['label' => $model->pickcode, 'url' => ['view', 'id' => $model->pickcode]];
$this->params['breadcrumbs'][] = ['label' => '編集'];
?>
<div class="product-pickcode-update">

    <h1>編集</h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
