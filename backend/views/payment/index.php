<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/payment/index.php $
 * $Id: index.php 1981 2016-01-14 06:04:04Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

?>
<div class="payment-index">

    <h1>支払い方法</h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [

            [
                'attribute' => 'payment_id',
                'format'    => 'html',
                'value'     => function($data)
                {
                    $id = sprintf('%06d',$data->payment_id);
                    return Html::a($id, ['view','id'=>$id]);
                },
            ],
            'name',
            'delivery:boolean',
            'datetime:boolean',
        ],
    ]); ?>

    <p>
        <?= Html::a('追加', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

</div>
