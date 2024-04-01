<?php

use yii\helpers\Html;
use common\models\Membership;

/* @var $this yii\web\View */
/* @var $model common\models\CustomerMembership */

$this->title = 'HJ代理店割引率を修正';
$this->params['breadcrumbs'][] = ['label'=>'顧客','url'=>['/customer']];
$this->params['breadcrumbs'][] = ['label'=>$model->customer->name,'url'=>['/customer/view','id'=>$model->customer_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-membership-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
