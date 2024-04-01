<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/agency-office/view.php $
 * $Id: view.php 3713 2017-10-27 00:28:32Z kawai $
 *
 * @var $this yii\web\View
 * @var $model common\models\AgencyOffice
 */

$this->params['breadcrumbs'][] = ['label' => ArrayHelper::getValue($model,'customer.name'), 'url' => ['/customer/view','id'=>$model->customer_id]];

?>
<div class="agency-office-view">

    <p class="pull-right">
        <?= Html::a('修正', ['update', 'id' => $model->customer_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <h1><?= ArrayHelper::getValue($model, 'customer.name') ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'customer_id',
                'format'    => 'html',
                'value'     => Html::a($model->customer_id, ['/customer/view','id'=>$model->customer_id]),
            ],
            'office_id',
            'company_name',
            'person_name',
            'zip',
            'addr',
            'tel',
            'fax',
            [
                'attribute' => 'payment_date',
                'format'    => 'html',
                'value'     => $model->getPaymentDays($model->payment_date),
            ],
        ],
    ]) ?>

</div>
