<?php

use yii\helpers\Html;
use \yii\helpers\ArrayHelper;
use yii\grid\GridView;
use \common\models\Vegetable;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/event-campaign/index.php $
 * $Id: index.php 2931 2016-10-07 04:44:08Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->params['breadcrumbs'][] = ['label' => '一覧'];
?>
<div class="campaign-index">
    <div class="col-md-12">
        <h1>イベント参加者限定キャンペーン</h1>
        <p class="pull-right">
            <?= Html::a('追加', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'layout'       => '{pager}{summary}{items}{pager}',
            'columns' => [
                [
                    'attribute' => 'campaign_code',
                    'label'     => 'キャンペーンコード',
                    'format'    => 'raw',
                    'headerOptions' =>['class'=>'col-md-1'],
                    'value'     => function ($data) {
                        return Html::a($data->campaign_code, ['view','id' => $data->ecampaign_id]);
                    }
                ],
                [
                    'attribute' => 'subcategory_id',
                    'value'     => function($data) { return $data->subcategory->name; }
                ],
                [
                    'attribute' => 'subcategory_id2',
                    'value'     => function($data) { return ($data->subcategory2) ? $data->subcategory2->name : ''; }
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
            ],
        ]); ?>
    </div>
</div>
