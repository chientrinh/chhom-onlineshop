<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy-price-range/update.php $
 * $Id: update.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RemedyPriceRange */

$this->title = 'Update Remedy Price Range: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Remedy Price Ranges', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->prange_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="remedy-price-range-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
