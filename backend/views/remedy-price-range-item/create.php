<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy-price-range-item/create.php $
 * $Id: create.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RemedyPriceRangeItem */

$this->title = 'Create Remedy Price Range Item';
$this->params['breadcrumbs'][] = ['label' => 'Remedy Price Range Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="remedy-price-range-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
