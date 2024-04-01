<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Vegetable;
use common\models\ChangeLog;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/vegetable/view.php $
 * $Id: view.php 2933 2016-10-08 02:47:03Z mori $
 *
 * @var $this yii\web\View
 * @var $campaign common\models\Campaign
 */

$jscode = "

    $('.js-zenkaku-to-hankaku').on('change', function(){
        $(this).val(charactersChange($(this).val()));
    });

    charactersChange = function(val){
        var han = val.replace(/[Ａ-Ｚａ-ｚ０-９：]/g,function(s){return String.fromCharCode(s.charCodeAt(0)-0xFEE0)});

        if(val.match(/[Ａ-Ｚａ-ｚ０-９：]/g)){
            return han;
        }

        return val;
    }
"; 
$this->registerJs($jscode);

$csscode = "
    p .btnArea > a {
        margin-top: 20px;
        margin-left: 0px;
        margin-right: 20px;
    }
    .btn-green { 
        color: #FFF;
        background-color: #85BAA6; 
    }
";
$this->registerCss($csscode);

$this->params['breadcrumbs'][] = ['label' => $campaign->campaign_name];

?>

<div class="campaign-view">

    <p class="pull-right">
        <?= Html::a('修正', ['update', 'id' => $campaign->campaign_id, 'target' => 'base'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('削除', ['delete', 'id' => $campaign->campaign_id], ['class' => 'btn btn-danger',
            'data' => [
                'confirm' => '本当にこのキャンペーン情報を削除してもいいですか',
            ],]) ?>
    </p>

    <h1><?= $campaign->campaign_name ?></h1>

    <?= yii\widgets\DetailView::widget([
        'model' => $campaign,
        'attributes' => [
                // [
                //     // 'attribute' => 'campaign_id',
                //     'label'     => 'ID',
                //     'value'     => $campaign->campaign_id,
                //     'headerOptions' =>['class'=>'js-zenkaku-to-hankaku', 'col-md-1'],
                // ],
                [
                    'attribute' => 'campaign_code',
                    'label'     => 'キャンペーンコード',
                    'value'     => $campaign->campaign_code,
                    'headerOptions' =>['class'=>'col-md-1'],
                ],
                [
                    'attribute' => 'campaign_name',
                    'label'     => '名称',
                    'value'     => $campaign->campaign_name,
                    'headerOptions' =>['class'=>'col-md-2'],
                ],
                [
                    'attribute' => 'start_date',
                    'label'     => '利用開始日時',
                    'format'    => ['date','php:Y-m-d D H:i:s'],
                    'value'     => $campaign->start_date,
                    'headerOptions' =>['class'=>'col-md-2'],
                ],
                [
                    'attribute' => 'end_date',
                    'label'     => '利用終了日時',
                    'format'    => ['date','php:Y-m-d D H:i:s'],
                    'value'     => $campaign->end_date,
                    'headerOptions' =>['class'=>'col-md-2'],
                ],
                [
                    'attribute' => 'status',
                    'label'     => '有効/無効',
                    'format'    => 'raw',
                    'value'     => 
                         Html::a(Html::tag(
                                    'button', 
                                    $campaign->statuses[$campaign->status], 
                                    ['class' => $campaign->isActiveOnlyStatus() ? 'btn btn-primary' : 'btn btn-danger']
                                ), [
                                    'changestatus',
                                    'id'=>$campaign->campaign_id, 
                                    'target' => Yii::$app->request->get('target', 'viewCategory'), 

                                ], ['title' => 'ステータスを変更します']),
                    'headerOptions' =>['class'=>'col-md-2'],
                ],
                [
                    'attribute' => 'branch_id',
                    'label'     => '拠点',
                    'value'     => $campaign->branch ? $campaign->branch->name : null,
                    'headerOptions' =>['class'=>'col-md-2'],
                ],
                [
                    'attribute' => 'streaming_id',
                    'label'     => '配信ID',
                    'value'     => $campaign->streaming ? $campaign->streaming->name : null,
                    'headerOptions' =>['class'=>'col-md-2'],
                ],             
                [
                    'attribute' => 'free_shipping1',
                    'value'     => $campaign->free_shipping1 ? "送料無料" : "",
                    'headerOptions' =>['class'=>'col-md-2'],
                ],                
                [
                    'attribute' => 'free_shipping2',
                    'value'     => $campaign->free_shipping2 ? "送料無料" : "",
                    'headerOptions' =>['class'=>'col-md-2'],
                ],                
                [
                    'attribute' => 'pre_order',
                    'value'     => $campaign->pre_order ? "事前注文受付" : "通常",
                    'headerOptions' =>['class'=>'col-md-2'],
                ],                                   
                'create_date',
                'update_date',
        ],
    ]) ?>

    <div class="col-md-9">
        <div class="row">
            <?= $this->render($viewFile, ['campaign'=>$campaign,'dataProvider'=>$dataProvider]); ?>
        </div>
    </div>

</div>
