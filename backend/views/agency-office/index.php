<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/agency-office/index.php $
 * $Id: index.php 3713 2017-10-27 00:28:32Z kawai $
 *
 * @var $this yii\web\View
 * @var $model common\models\AgencyOffice
 */

$days = $searchModel->getPaymentDays();

?>
<div class="agency-office-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'layout' => '{items}{pager}',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'customer_id',
            'customer.name',
            'company_name',
            'person_name',
            'addr',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}{update}',
            // view,updateの各アクションに顧客IDを引き渡す処理が無かったため、buttons以降を追加
                    'buttons' => [
                    'view'   => function($url, $model, $key) { return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',['/agency-office/view','id'=>$model->customer_id]);},
                    'update' => function($url, $model, $key) { return Html::a('<span class="glyphicon glyphicon-pencil"></span>',['/agency-office/update','id'=>$model->customer_id]);}
                ],
            ],
            [
                'attribute' => 'payment_date',
                'format'    => 'html',
                'value'     => function($model) { return $model->getPaymentDays($model->payment_date); },
                'filter'    => $days,
            ],
        ],
    ]); ?>

</div>
