<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CustomerMembership */

$this->title = '所属を追加';
$this->params['breadcrumbs'][] = ['label'=>'顧客','url'=>['/customer']];
$this->params['breadcrumbs'][] = ['label'=>($c = $model->customer) ? $c->name : null,'url'=>['/customer/view','id'=>$model->customer_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-membership-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
