<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/event-campaign/view.php $
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

$this->params['breadcrumbs'][] = ['label' => $campaign->campaign_code];

?>

<div class="campaign-view">

    <p class="pull-right">
        <?= Html::a('修正', ['update', 'id' => $campaign->ecampaign_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('削除', ['delete', 'id' => $campaign->ecampaign_id], ['class' => 'btn btn-danger',
            'data' => [
                'confirm' => '本当にこのキャンペーン情報を削除してもいいですか',
            ],]) ?>
    </p>

    <h1><?= $campaign->campaign_code ?></h1>

    <?= yii\widgets\DetailView::widget([
        'model' => $campaign,
        'attributes' => [
                [
                    'attribute' => 'campaign_code',
                    'label'     => 'キャンペーンコード',
                    'value'     => $campaign->campaign_code,
                    'headerOptions' =>['class' => 'col-md-1'],
                ],
                [
                    'attribute' => 'subcategory_id',
                    'label'     => 'サブカテゴリ',
                    'value'     => $campaign->subcategory->name,
                    'headerOptions' =>['class' => 'col-md-2'],
                ],
                [
                    'attribute' => 'subcategory_id2',
                    'label'     => 'サブカテゴリ2',
                    'value'     => ($campaign->subcategory2) ? $campaign->subcategory2->name : '',
                    'headerOptions' =>['class' => 'col-md-2'],
                ],
                [
                    'attribute' => 'start_date',
                    'label'     => '利用開始日時',
                    'format'    => ['date','php:Y-m-d H:i:s'],
                    'value'     => $campaign->start_date,
                    'headerOptions' =>['class' => 'col-md-2'],
                ],
                [
                    'attribute' => 'end_date',
                    'label'     => '利用終了日時',
                    'format'    => ['date','php:Y-m-d H:i:s'],
                    'value'     => $campaign->end_date,
                    'headerOptions' =>['class' => 'col-md-2'],
                ],
                'create_date',
                'update_date',
        ],
    ]) ?>
</div>
