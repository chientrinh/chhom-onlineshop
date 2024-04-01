<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/payment/view.php $
 * $Id: view.php 1981 2016-01-14 06:04:04Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Payment
 */

$this->params['breadcrumbs'][] = ['label' => $model->name];
?>
<div class="payment-view">

    <p class="pull-right">
        <?= Html::a('編集', ['update', 'id' => $model->payment_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <h1><?= $model->name ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'payment_id',
            'name',
            'delivery:boolean',
            'datetime:boolean',
        ],
    ]) ?>

</div>
