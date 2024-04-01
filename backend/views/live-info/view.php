<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use \common\models\LiveInfo;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/live-info/view.php $
 * $Id: view.php 2992 2016-10-19 07:37:38Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Streaming
 */

$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view','id'=>$model->info_id]];
$campaign_type = [1 => '通常', 2 => 'コングレス', 3 => 'シンポジウム'];

?>
<div class="live-info-view">

    <p class="pull-right">
        <?= Html::a('編集', ['update', 'id' => $model->info_id], ['class' => 'btn btn-primary']) ?>
        <?= !$model->isNewRecord ? \yii\helpers\Html::a("削除", ['live-info/delete','id'=>$model->info_id],['class'=>'btn btn-danger', 'title'=>'確認', 'data' =>['confirm'=>"この追加情報を削除します。よろしいですか？"]]) : null ?>
        <?= Html::a('',['view','id'=>$model->info_id -1],['class'=>'btn btn-sm btn-default glyphicon glyphicon-chevron-left'])?>
        <?= Html::a('',['view','id'=>$model->info_id +1],['class'=>'btn btn-sm btn-default glyphicon glyphicon-chevron-right'])?>
    </p>

    <h1><?= Html::encode($model->name) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'info_id',
            [
                'attribute' => 'name',
                'format'    => 'raw',
                'value'     => Html::a($model->name, ['product/view', 'id'=>$model->info_id]),
            ],
            [
                'attribute' => 'place',
                'format'    => 'raw',
                'value'     => $model->place,
            ],
            [
                'attribute' => 'option_name',
                'format'    => 'raw',
                'value'     => $model->option_name,
            ],
            [
                'attribute' => 'option_description',
                'format'    => 'text',
                'value'     => $model->option_description,
            ],
            [
                'attribute' => 'online_option_enable',
                'format'    => 'text',
                'value'     => $model->online_option_enable ? '可' : '不可',
            ],
            [
                'attribute' => 'product_id',
                'format'    => 'raw',
                'value'     => $model->product_id ? Html::a($model->product_id. ' : '.$model->product->name, ['product/view', 'id'=>$model->product_id]) : "",
            ],
            // [
            //     'attribute' => 'option_price',
            //     'format'    => 'text',
            //     'value'     => $model->option_price,
            // ],
            [
                'attribute' => 'coupon_name',
                'format'    => 'raw',
                'value'     => $model->coupon_name,
            ],
            [
                'attribute' => 'coupon_code',
                'format'    => 'raw',
                'value'     => $model->coupon_code,
            ],
            [
                'attribute' => 'coupon_discount',
                'format'    => 'currency',
                'value'     => $model->coupon_discount,
            ],
            [
                'attribute' => 'online_coupon_enable',
                'format'    => 'text',
                'value'     => $model->online_coupon_enable ? '可' : '不可',
            ],
            [
                'attribute' => 'companion',
                'format'    => 'text',
                'value'     => $model->companion,
            ],
            [
                'attribute' => 'adult_price1',
                'format'    => 'currency',
                'value'     => $model->adult_price1,
            ],
            [
                'attribute' => 'adult_price2',
                'format'    => 'currency',
                'value'     => $model->adult_price2,
            ],
            [
                'attribute' => 'adult_price3',
                'format'    => 'currency',
                'value'     => $model->adult_price3,
            ],
            [
                'attribute' => 'child_price1',
                'format'    => 'currency',
                'value'     => $model->child_price1,
            ],
            [
                'attribute' => 'child_price2',
                'format'    => 'currency',
                'value'     => $model->child_price2,
            ],
            [
                'attribute' => 'child_price3',
                'format'    => 'currency',
                'value'     => $model->child_price3,
            ],
            [
                'attribute' => 'infant_price1',
                'format'    => 'currency',
                'value'     => $model->infant_price1,
            ],
            [
                'attribute' => 'infant_price2',
                'format'    => 'currency',
                'value'     => $model->infant_price2,
            ],
            [
                'attribute' => 'infant_price3',
                'format'    => 'currency',
                'value'     => $model->infant_price3,
            ],
            [
                'attribute' => 'capacity',
                'format'    => 'raw',
                'value'     => $model->capacity,
            ],
            [
                'attribute' => 'subscription',
                'format'    => 'raw',
                'value'     => $model->subscription,
            ],
            [
                'attribute' => 'campaign_code',
                'format'    => 'raw',
                'value'     => $model->campaign_code,
            ],
            [
                'attribute' => 'campaign_type',
                'format'    => 'raw',
                'value'     => $model->campaign_type ? $campaign_type[$model->campaign_type] : "",
            ],
            [
                'attribute' => 'campaign_period',
                'format'    => 'raw',
                'value'     => $model->campaign_period,
            ],
            [
                'attribute' => 'pre_order_code',
                'format'    => 'raw',
                'value'     => $model->pre_order_code ? $model->pre_order_code : "",
            ],
            [
                'attribute' => 'pre_order_period',
                'format'    => 'raw',
                'value'     => $model->pre_order_period ? $model->pre_order_period : "",
            ],
            [
                'attribute' => 'support_entry',
                'format'    => 'raw',
                'value'     => $model->support_entry ? '使用する' : '使用しない',
            ],
            'create_date',
            'update_date',
            'expire_date',
        ],
    ]) ?>

    <h2>表示するチケット</h2>
    <?= Html::a('チケットの追加', ['live-item-info/create'], ['class' => 'btn btn-success pull-right']) ?>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getLiveItemInfos(),
            'sort'  => ['defaultOrder' => ['info_id'=>SORT_DESC]],
        ]),
        'columns' => [
            [
                'attribute' => 'product_id',
                'format'    => 'html',
                'value'     => function($data)
                {
                    return $data->product_id.' : '.Html::a($data->product->name, ['product/view','id'=>$data->product_id]).'　　'.Html::a('チケットの変更', ['live-item-info/update', 'id' => $data->id], ['class' => 'btn btn-primary pull-right']);
                },
            ],
        ],
    ]) ?>
</div>
