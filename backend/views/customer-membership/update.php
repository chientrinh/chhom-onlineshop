<?php

use yii\helpers\Html;
use common\models\Membership;

/* @var $this yii\web\View */
/* @var $model common\models\CustomerMembership */

$this->title = '所属を修正';
$this->params['breadcrumbs'][] = ['label'=>'顧客','url'=>['/customer']];
$this->params['breadcrumbs'][] = ['label'=>$model->customer->name,'url'=>['/customer/view','id'=>$model->customer_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-membership-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <?php if(in_array($model->membership_id, [
        Membership::PKEY_TORANOKO_GENERIC,
        Membership::PKEY_TORANOKO_NETWORK,
        Membership::PKEY_TORANOKO_FAMILY,
    ])): ?>
        とらのこ更新手続きはこちらからどうぞ
        <?= Html::a("とらのこ更新手続き",['/member/toranoko/update',
                    'id'=>$model->customer->parent
                        ? $model->customer->parent->customer_id
                        : $model->customer->customer_id,
        ],['class'=>'btn btn-warning']) ?>
    <?php endif ?>

</div>
