<?php

use yii\helpers\Html;
use \yii\helpers\ArrayHelper;
use yii\grid\GridView;
use \common\models\Vegetable;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/vegetable/index.php $
 * $Id: index.php 2931 2016-10-07 04:44:08Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->params['breadcrumbs'][] = ['label' => '一覧'];//, 'url' => ['index'] ];
?>
<div class="campaign-index">
    <div class="col-md-12">
        <h1>キャンペーン</h1>
        <p class="pull-right">
            <?= Html::a('追加', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    
        <?= GridView::widget([  
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'layout'       => '<strong>※キャンペーンコードの先頭がd: 値引、p:ポイント</strong><p>{pager}{summary}{items}{pager}',
            'columns' => [
                [
                    'attribute' => 'campaign_code',
                    'label'     => 'キャンペーンコード',
                    'headerOptions' =>['class'=>'col-md-1'],
                ],
                [
                    'attribute' => 'campaign_type',
                    'label'     => 'キャンペーン区分',
                    'format'    => 'raw',
                    'value'     => function($data) use($searchModel){ 
                                       return $searchModel->types[$data->campaign_type];
                                   },
                    'filter'    => $searchModel->types,
                    'headerOptions' =>['class'=>'col-md-1'],
                ],
                [
                    'attribute' => 'campaign_name',
                    'label'     => '名称',
                    'format'    => 'raw',
                    'value'     => function($data){ 
                        return Html::a($data->campaign_name, ['view','id'=>$data->campaign_id, 'target' => 'viewCategory']);
                    },
                    'headerOptions' =>['class'=>'col-md-2'],
                ],
                [
                    'attribute' => 'start_date',
                    'label'     => '利用開始日時',
                    'format'    => ['date','php:Y-m-d D H:i:s'],
                    'value'     => function($data){ return $data->start_date; },
                    'filter'    => \yii\jui\DatePicker::widget([
                                        'model'      => $searchModel,
                                        'attribute'  =>'start_date',
                                        'language'   => 'ja',
                                        'dateFormat' => 'yyyy-MM-dd',
                                        'options'    => ['class'=>'form-control col-md-12'],
                    ]),
                    'headerOptions' =>['class'=>'col-md-2'],
                ],
                [
                    'attribute' => 'end_date',
                    'label'     => '利用終了日時',
                    'format'    => ['date','php:Y-m-d D H:i:s'],
                    'value'     => function($data){ return $data->end_date; },
                    'filter'    => \yii\jui\DatePicker::widget([
                                        'model'      => $searchModel,
                                        'attribute'  =>'end_date',
                                        'language'   => 'ja',
                                        'dateFormat' => 'yyyy-MM-dd',
                                        'options'    => ['class'=>'form-control col-md-12'],
                    ]),
                    'headerOptions' =>['class'=>'col-md-2'],
                ],
                [
                    'attribute' => 'status',
                    'label'     => '有効/無効',
                    'value'     => function($data) use($searchModel) { 
                        return $searchModel->statuses[$data->status]; 
                    },
                    'filter'    => $searchModel->statuses,
                    'headerOptions' =>['class'=>'col-md-2'],
                ],
                [
                    'attribute' => 'branch_id',
                    'label'     => '拠点',
                    'value'     => function($data){ 
                        return $data->branch ? $data->branch->name : null; 
                    },
                    'filter'    => ArrayHelper::map(\common\models\Branch::find()->forCampaign()->All(), 'branch_id', 'name'),
                    'headerOptions' =>['class'=>'col-md-2'],
                ],
                [
                    'attribute' => 'streaming_id',
                    'label'     => '配信ID',
                    'value'     => function($data){
                        return $data->streaming ? $data->streaming->name : null;
                    },
                    'filter'    => $streamings,
                    'headerOptions' =>['class'=>'col-md-2'],
                ],                
                [
                    'attribute' => 'free_shipping1',
                    'value'     => function($data){
                        return $data->free_shipping1 ? "送料無料" : "";
                    },
                    'filter'    => $shippings,
                    'headerOptions' =>['class'=>'col-md-2'],
                ],                
                [
                    'attribute' => 'free_shipping2',
                    'value'     => function($data){
                        return $data->free_shipping2 ? "送料無料" : "";
                    },
                    'filter'    => $shippings,
                    'headerOptions' =>['class'=>'col-md-2'],
                ],                
                [
                    'attribute' => 'pre_order',
                    'value'     => function($data){
                        return $data->pre_order ? "事前注文受付" : "通常";
                    },
                    'filter'    => $preorders,
                    'headerOptions' =>['class'=>'col-md-2'],
                ],                
            ],
        ]); ?>
    </div>
</div>
