<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy-price-range-item/update.php $
 * $Id: update.php 2044 2016-02-05 08:10:40Z mori $
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RemedyPriceRangeItem */

$this->params['breadcrumbs'][] = ['label'=>'編集'];
?>
<div class="remedy-price-range-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
