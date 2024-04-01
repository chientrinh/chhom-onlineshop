<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/customer-addrbook/update.php $
 * $Id: update.php 2994 2016-10-20 05:03:22Z mori $
 * @var $this yii\web\View
 * @var $model common\models\CustomerAddrbook
 */

use yii\helpers\Html;
use yii\helpers\Url;


$customer = $model->customer;
$this->params['breadcrumbs'][] = ['label' => '顧客', 'url' => ['/customer/index']];
$this->params['breadcrumbs'][] = ['label' => $customer->name, 'url' => ['/customer/view','id'=>$customer->customer_id]];
$this->params['breadcrumbs'][] = ['label' => '住所録', 'url' => ['view','id'=>$customer->customer_id]];

$this->params['breadcrumbs'][] = ['label' => '編集', 'url' => Url::current() ];
?>
<div class="customer-addrbook-update">

    <h1><?= $customer->name ?><small>さん 住所録</small></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
