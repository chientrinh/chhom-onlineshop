<?php

use yii\helpers\Html;

/**
 * $URL$
 * $Id$
 * 
 * @var $this yii\web\View
 * @var $model backend\models\CustomerInfo
 */

$this->title = '付記を追加';
$this->params['breadcrumbs'][] = ['label'=>'顧客','url'=>['/customer']];
$this->params['breadcrumbs'][] = ['label'=>$model->customer->name,'url'=>['/customer/view','id'=>$model->customer_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-membership-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
