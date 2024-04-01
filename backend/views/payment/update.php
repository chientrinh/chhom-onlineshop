<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/payment/update.php $
 * $Id: update.php 1981 2016-01-14 06:04:04Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Payment
 */

$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->payment_id]];
$this->params['breadcrumbs'][] = '編集';
?>
<div class="payment-update">

    <h1>支払い方法 #<?= $model->payment_id ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
