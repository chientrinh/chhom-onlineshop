<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product-pickcode/create.php $
 * $Id: create.php 2485 2016-05-03 04:46:04Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\ProductPickcode
 */

$this->params['breadcrumbs'][] = ['label' => '追加'];

?>

<div class="product-pickcode-create">

    <h1>追加</h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
