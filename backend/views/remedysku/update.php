<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedysku/update.php $
 * $Id: update.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RemedySku */

$this->title = 'Update Remedy Sku: ' . ' ' . $model->sku_id;
$this->params['breadcrumbs'][] = ['label' => 'Remedy Skus', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->sku_id, 'url' => ['view', 'id' => $model->sku_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="remedy-sku-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
