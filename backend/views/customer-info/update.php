<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/customer-info/update.php $
 * $Id: update.php 2737 2016-07-17 08:06:54Z mori $
 * 
 * @var $this yii\web\View
 * @var $model backend\models\CustomerInfo
 */

$this->title = sprintf('付記を修正: %s', $model->customer->name);
$this->params['breadcrumbs'][] = ['label' => '顧客', 'url' => ['/customer/index']];
$this->params['breadcrumbs'][] = ['label' => $model->customer->name, 'url' => ['/customer/view', 'id' => $model->customer_id]];
$this->params['breadcrumbs'][] = '付記を修正';
?>
<div class="customer-membership-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
