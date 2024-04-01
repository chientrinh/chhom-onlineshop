<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/live-info/index.php $
 * $Id: index.php 2286 2020-04-28 11:28:00Z kawai $
 *
 * @var $this yii\web\View
 * @var $searchModel common\models\LiveInfoSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */
$onlineEnable = [0 => '不可', 1 => '可'];
$event_type = [0 => '無料', 1 => '有料'];
$campaign_type = [0 => 'なし', 1 => '通常', 2 => 'コングレス', 3 => 'シンポジウム'];
$support_entry = [0 => '使用しない', 1 => '使用する'];

?>
<div class="live-info-index">

    <h1>ライブ配信追加情報</h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
            [
                'attribute' => 'info_id',
                 'headerOptions' => [
                // this should be on a CSS file as class instead of a inline style attribute...
                'style' => 'text-align: center !important;vertical-align: middle !important'
                 ],
            ],
            [
                'label' => '表示するチケット',
                'format'    => 'html',
                'value'     =>  function($data){
                    $liveItems = $data->getLiveItemInfos()->all();
                    if(count($liveItems) > 0) {
                        $products = [];
                        foreach($liveItems as $data) {
                            $products[] = $data->product_id.' : '.Html::a($data->product->name, ['product/view','id'=>$data->product_id]);
                        }
                        return implode ( "<br />" ,$products);
                          
                    } else {
                        return "";
                    }
                },
                 'headerOptions' => [
                // this should be on a CSS file as class instead of a inline style attribute...
                'style' => 'text-align: center !important;vertical-align: middle !important'
                 ],
            ],
            [
                'attribute' => 'name',
                'format'    => 'raw',
                'value'     => function($data){ return Html::a($data->name, ['view', 'id'=>$data->info_id]); },
                 'headerOptions' => [
                // this should be on a CSS file as class instead of a inline style attribute...
                'style' => 'text-align: center !important;vertical-align: middle !important'
                 ],
            ],
            [
                'attribute' => 'option_name',
                'format'    => 'text',
                'value'     => function($data){ return $data->option_name; },
                 'headerOptions' => [
                // this should be on a CSS file as class instead of a inline style attribute...
                'style' => 'text-align: center !important;vertical-align: middle !important'
                 ],
            ],
            [
                'attribute' => 'option_description',
                'format'    => 'text',
                'value'     => function($data){ return $data->option_description; },
                 'headerOptions' => [
                // this should be on a CSS file as class instead of a inline style attribute...
                'style' => 'text-align: center !important;vertical-align: middle !important'
                 ],                
            ],
            [
                'label'     => '自宅受講時<br>オプション',
                'encodeLabel' => false,
                'attribute' => 'online_option_enable',
                'format'    => 'text',
                'filter'    => $onlineEnable,
                'value'     => function($data){ return $data->online_option_enable ? '可' : '不可'; },
                 'headerOptions' => [
                // this should be on a CSS file as class instead of a inline style attribute...
                'style' => 'text-align: center !important;vertical-align: middle !important'
                 ],
            ],
            [
                'attribute' => 'product_id',
                'format'    => 'raw',
                'value'     => function($data){ return $data->product_id ? $data->product_id.' : '.Html::a($data->product->name, ['product/view', 'id'=>$data->product_id]) : ""; },
                 'headerOptions' => [
                // this should be on a CSS file as class instead of a inline style attribute...
                'style' => 'text-align: center !important;vertical-align: middle !important'
                 ],
            ],
            [
                'attribute' => 'coupon_name',
                'format'    => 'raw',
                'value'     => function($data){ return $data->coupon_name;},
                 'headerOptions' => [
                // this should be on a CSS file as class instead of a inline style attribute...
                'style' => 'text-align: center !important;vertical-align: middle !important'
                 ],
            ],
            [
                'attribute' => 'coupon_code',
                'format'    => 'raw',
                'value'     => function($data){ return $data->coupon_code;},
                 'headerOptions' => [
                // this should be on a CSS file as class instead of a inline style attribute...
                'style' => 'text-align: center !important;vertical-align: middle !important'
                 ],                
            ],
            [
                'attribute' => 'coupon_discount',
                'format'    => 'currency',
                'value'     => function($data){ return $data->coupon_discount;},
                 'headerOptions' => [
                // this should be on a CSS file as class instead of a inline style attribute...
                'style' => 'text-align: center !important;vertical-align: middle !important'
                 ],
            ],
            [
                'label'     => '自宅受講時<br>クーポン',
                'encodeLabel' => false,
                'attribute' => 'online_coupon_enable',
                'format'    => 'text',
                'filter'    => $onlineEnable,                
                'value'     => function($data){ return $data->online_coupon_enable ? '可' : '不可'; },
                 'headerOptions' => [
                // this should be on a CSS file as class instead of a inline style attribute...
                'style' => 'text-align: center !important;vertical-align: middle !important'
                 ],
            ],
            [
                'label'     => 'イベント',
                'attribute' => 'companion',
                'format'    => 'text',
                'filter'    => $event_type,                
                'value'     => function($data){ return $data->companion ? "有料" : "無料";},
                 'headerOptions' => [
                // this should be on a CSS file as class instead of a inline style attribute...
                'style' => 'text-align: center !important;vertical-align: middle !important'
                 ],                
            ],
            [
                'attribute' => 'capacity',
                'format'    => 'raw',
                'value'     => function($data){ return $data->capacity ? $data->capacity."人" : "";},
                 'headerOptions' => [
                // this should be on a CSS file as class instead of a inline style attribute...
                'style' => 'text-align: center !important;vertical-align: middle !important'
                 ],                
            ],
            [
                'attribute' => 'subscription',
                'format'    => 'raw',
                'value'     => function($data){ return $data->subscription ? $data->subscription."人" : "";},
                 'headerOptions' => [
                // this should be on a CSS file as class instead of a inline style attribute...
                'style' => 'text-align: center !important;vertical-align: middle !important'
                 ],                
            ],
            [
                'attribute' => 'campaign_code',
                'format'    => 'raw',
                'value'     => function($data){ return $data->campaign_code ? $data->campaign_code : "";},
                 'headerOptions' => [
                // this should be on a CSS file as class instead of a inline style attribute...
                'style' => 'text-align: center !important;vertical-align: middle !important'
                 ],                
            ],
            [
                'attribute' => 'campaign_type',
                'format'    => 'raw',
                'filter'    => $campaign_type,
                'value'     => function($data) use ($campaign_type){ return $data->campaign_type ? $campaign_type[$data->campaign_type] : "";},
                 'headerOptions' => [
                // this should be on a CSS file as class instead of a inline style attribute...
                'style' => 'text-align: center !important;vertical-align: middle !important'
                 ],                
            ],
            [
                'attribute' => 'campaign_period',
                'format'    => 'raw',
                'value'     => function($data){ return $data->campaign_period ? $data->campaign_period : "";},
                 'headerOptions' => [
                // this should be on a CSS file as class instead of a inline style attribute...
                'style' => 'text-align: center !important;vertical-align: middle !important'
                 ],                
            ],
            [
                'attribute' => 'pre_order_code',
                'format'    => 'raw',
                'value'     => function($data){ return $data->pre_order_code ? $data->pre_order_code : "";},
                 'headerOptions' => [
                // this should be on a CSS file as class instead of a inline style attribute...
                'style' => 'text-align: center !important;vertical-align: middle !important'
                 ],                
            ],
            [
                'attribute' => 'pre_order_period',
                'format'    => 'raw',
                'value'     => function($data){ return $data->pre_order_period ? $data->pre_order_period : "";},
                 'headerOptions' => [
                // this should be on a CSS file as class instead of a inline style attribute...
                'style' => 'text-align: center !important;vertical-align: middle !important'
                 ],                
            ],
            [
                'label'     => 'サポート申込',
                'attribute' => 'support_entry',
                'format'    => 'text',
                'filter'    => $support_entry,                
                'value'     => function($data){ return $data->support_entry ? "使用する" : "使用しない";},
                 'headerOptions' => [
                // this should be on a CSS file as class instead of a inline style attribute...
                'style' => 'text-align: center !important;vertical-align: middle !important'
                 ],                
            ],
            [
                'attribute' => 'expire_date',
                'format'    => 'text',
                'value'     => function($data){ return $data->expire_date; },
                 'headerOptions' => [
                // this should be on a CSS file as class instead of a inline style attribute...
                'style' => 'text-align: center !important;vertical-align: middle !important'
                 ],
            ],
            [
                'class' => \yii\grid\ActionColumn::className(),
                'template' => '{view}{update}',
            ],

        ],
    ]); ?>

    <?= Html::a('追加', ['create'], ['class' => 'btn btn-success']) ?>

</div>
