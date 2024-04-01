<?php
/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/sales/views/ecorange/index.php $
 * @version $Id: index.php 1023 2015-05-20 10:16:53Z mori $
 */

$shops = \common\models\ecorange\Baseinfo::find()->all();

$dataProvider->pagination->pageSize = 100;

?>
<div class="sales-ecorange-index">
   <h1><?= \yii\helpers\Html::a('売上の明細', ['/sales']) ?></h1>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'layout' => '{summary}{pager}{items}{pager}',
        'columns' => [
            [ 'class' => 'yii\grid\SerialColumn' ],
            [
                'attribute' => 'shop_id',
                'filter'    => \yii\helpers\ArrayHelper::map($shops, 'shop_id', 'name'),
                'value'     => function($data){ return $data->shop->name; },
            ],
            [  'attribute' => 'create_date',
               'format'    => 'html',
                   'value'     => function($data){ if($data->order)return \yii\helpers\Html::a(substr($data->order->create_date,0,10), sprintf('http://shop.homoeopathy.ac/admin/tihm/index.php?r=order/view&id=%s',$data->order_id), ['title'=>$data->order->create_date]); }
            ],
            [  'attribute' => 'product_code',
               'format'    => 'html',
                   'value'     => function($data){ return \yii\helpers\Html::a($data->product_code, sprintf('http://shop.homoeopathy.ac/admin/tihm/index.php?r=product/view&id=%s',$data->product_id)); }
            ],
            'product_name',
            'quantity',
            [
                'attribute'=> 'price',
                    'format' => 'currency',
                    'contentOptions' => ['class'=>'number'],
            ],
            [
                'attribute' => 'discount_rate',
                'value'     => function($data){ return $data->discount_rate ? $data->discount_rate : ''; },
                    'contentOptions' => ['class'=>'number']
            ],
            [
                'attribute' => 'discount_price',
                'value'     => function($data){ return $data->discount_price ? $data->discount_price : ''; },
                'contentOptions' => ['class'=>'number']
            ],
            [
                'attribute' => 'point_rate',
                'value'     => function($data){ return $data->point_rate ? $data->point_rate : ''; },
                'contentOptions' => ['class'=>'number']
            ],
        ],
])?>          
</div>
