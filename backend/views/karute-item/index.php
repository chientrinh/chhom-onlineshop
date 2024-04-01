<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/karute-item/index.php $
 * $Id: index.php 2276 2016-03-20 06:58:20Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

?>
<div class="karute-item-index">

    <h1>子カルテ</h1>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'layout'  => '{items}{pager}{summary}',
        'columns' => [
            [
                'attribute' => 'syohoid',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->syohoid, ['view','id'=>$data->syohoid]); },
            ],
            'karuteid',
            'customerid',
            'syoho_date',
            'denpyo_centerid',
        ],
    ]); ?>

</div>
