<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/member/views/oasis/index.php $
 * $Id: index.php 1823 2015-11-27 06:09:44Z mori $
 */

use \yii\helpers\Html;

?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'name',
            'format' => 'html',
            'value'  => function($data)
            {
                return Html::a($data->name , ['/product/view', 'id'=>$data->product_id,'target'=>'sales']);
            },
        ],
        [
            'label'     => '配布終了日',
            'attribute' => 'expire_date',
            'format'    => 'date',
        ],
        [
            'label'  => '対象者',
            'format' => 'html',
            'value'  => function($data)
            {
                $text = $data->getCustomers(null)->count() . ' 名';
                return Html::a($text , ['view', 'pid'=>$data->product_id]);
            },
            'contentOptions' => ['class'=>'text-right'],
        ],
        [
            'label'  => '配布済み',
            'format' => 'html',
            'value'  => function($data)
            {
                $text = $data->getCustomers(true)->count() . ' 名';
                return Html::a($text , ['view', 'pid'=>$data->product_id, 'shipped'=>true ]);
            },
            'contentOptions' => ['class'=>'text-right'],
        ],
        [
            'label'  => '未発送',
            'format' => 'html',
            'value'  => function($data)
            {
                $text = $data->getCustomers(false)->count() . ' 名';
                return Html::a($text , ['view', 'pid'=>$data->product_id, 'shipped'=>false ]);
            },
            'contentOptions' => ['class'=>'text-right'],
        ],
    ],
]) ?>
