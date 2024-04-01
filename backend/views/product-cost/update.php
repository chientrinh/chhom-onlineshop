<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product-cost/update.php $
 * $Id: update.php 2307 2016-03-26 08:33:43Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->cost_id]];
$this->params['breadcrumbs'][] = '編集';
?>
<div class="product-cost-update">

    <h1><?= $model->name ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
