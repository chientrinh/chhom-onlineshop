<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product-cost/index.php $
 * $Id: index.php 2307 2016-03-26 08:33:43Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

?>
<div class="product-cost-index">

    <h1>製造原価</h1>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{view}{update}',
            ],
            'ean13',
            'name',
            [
                'attribute' => 'cost',
                'format' => ['decimal', 2],
                'contentOptions' => ['class'=>'text-right'],
            ],
            'start_date',
            'end_date',
            'created_by',
            'updated_by',
        ],
    ]); ?>

</div>
