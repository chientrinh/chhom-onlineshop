<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/customer/purchase.php $
 * $Id: purchase.php 3135 2016-12-03 06:00:13Z mori $
 *
 * @var $this yii\web\View
 * @var $provider yii\data\ActiveDataProvider
 */

$this->params['breadcrumbs'][] = ['label'=> '最終購入日', 'url'=> 'purchase'];

?>
<div class="customer-purchase">

    <p class="pull-right">
        <?= Html::a('CSV',['purchase','format'=>'csv'],['class'=>'btn btn-default']) ?>
    </p>

    <?= yii\grid\GridView::widget([
        'dataProvider' => $provider,
        'layout'       => '{items}{pager}{summary}',
        'pager'        => ['maxButtonCount' => 20],
        'summaryOptions' => ['class'=>'pull-right small text-muted'],
        'tableOptions' => ['class'=>'table table-condensed table-striped'],
        'columns'      => [
            [
                'attribute' => 'create_date',
                'label'     => '購入日',
                'format'    => 'html',
                'value'     => function($model){
                    $create_date = ArrayHelper::getValue($model,'create_date');

                    if($create_date)
                        return date('Y-m-d H:i', strtotime($create_date));
                },
                'headerOptions' => ['class'=>'col-md-2'],
            ],
            [
                'attribute' => 'customer_id',
                'format'    => 'html',
                'value'     => function($data){
                    $customer_id = ArrayHelper::getValue($data, 'customer_id');

                    if($customer_id)
                        return Html::a(sprintf('%06d',$customer_id), ['view','id'=>$customer_id]);
                },
                'headerOptions' => ['class'=>'col-md-1'],
            ],
            [
                'attribute' => 'customer_name',
                'label'     => 'お名前',
            ],
            [
                'attribute' => 'purchase_id',
                'format'    => 'html',
                'value'     => function($model){
                    $purchase_id = ArrayHelper::getValue($model,'purchase_id');

                    if($purchase_id)
                        return Html::a(sprintf('%06d',$purchase_id), ['/purchase/view', 'id'=> $purchase_id]);
                },
                'contentOptions' => ['class'=>'text-right'],
                'headerOptions' => ['class'=>'col-md-1'],
            ],
        ],
    ]); ?>

</div>
