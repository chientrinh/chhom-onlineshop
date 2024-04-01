<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product-cost/create.php $
 * $Id: create.php 2307 2016-03-26 08:33:43Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

?>
<div class="product-cost-create">

    <h1>追加</h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
