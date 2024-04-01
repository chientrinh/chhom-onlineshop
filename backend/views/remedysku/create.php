<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedysku/create.php $
 * $Id: create.php 804 2015-03-19 07:31:58Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\RemedySku
 */

$this->title = 'Create Remedy Sku';
$this->params['breadcrumbs'][] = ['label' => 'Remedy Skus', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="remedy-sku-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
