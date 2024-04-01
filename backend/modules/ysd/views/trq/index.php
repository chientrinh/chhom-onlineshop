<?php

use yii\helpers\Html;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/ysd/views/rrs/view.php $
 * @version $Id: view.php 1961 2016-01-11 01:39:26Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel \common\models\TransferRequest
 * @var $year     integer
 * @var $month    integer
 * @var $invoices integer
 */

$prev = (object) [
    'year'  => (1 == $month) ? ($year -1) : $year,
    'month' => (1 == $month) ? 12         : $month - 1,
    ];
$next = (object) [
    'year'  => (12 == $month) ? ($year +1) : $year,
    'month' => (12 == $month) ? 1          : $month + 1,
];

$summary = sprintf('%04d 年 %02d 月 分を表示しています', $year, $month);

?>

<div class="transfer-request-index">

    <div class="pull-right">
        <?= Html::a(Html::tag('span','',['class'=>'glyphicon glyphicon-chevron-left']),[
            'index',
            'year' => $prev->year,
            'month'=> $prev->month,
        ],['class'=>'btn btn-xs btn-default']) ?>

        <strong><?= sprintf('%04d-%02d', $year, $month) ?></strong>

        <?= Html::a(Html::tag('span','',['class'=>'glyphicon glyphicon-chevron-right']),[
            'index',
            'year' => $next->year,
            'month'=> $next->month,
        ],['class'=>'btn btn-xs btn-default']) ?>
    </div>

    <h1>振替依頼</h1>

    <p>
        <?= $summary ?>
    </p>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
            [
                'attribute' => 'trq_id',
                'format'    => 'html',
                'value'     => function($data)
                {
                    $label = sprintf('%06d', $data->trq_id);
                    $html  = Html::a($label, ['view','id'=>$data->trq_id]);

                    if(! $data->validate())
                        $html .= Html::tag('span','',['title'=>implode(';',$data->firstErrors),
                                                      'class'=>'glyphicon glyphicon-alert text-danger']);

                    return $html;
                },
            ],
            'cdate',
            [
                'attribute' => 'custno',
                'format'    => 'html',
                'value'     => function($data){
                    if($c = $data->customer)
                        return sprintf("%s (%s)", $data->custno, $c->name);

                    return $data->custno;
                }
            ],
            [
                'attribute' => 'charge',
                'format'    => 'currency',
                'contentOptions' => ['class' => 'text-right'],
            ],
            [
                'attribute' => 'pre',
                'format'    => 'boolean',
                'filter'    => [0 => 'いいえ', 1 => 'はい'],
            ],
            [
                'attribute' => 'response.stt',
                'value'     => function($data)
                {
                    $s = ($data->response ? $data->response->status : null);
                    return $s ? $s->name : '依頼中';
                },
            ],
            ['attribute'=>'created_at','format'=>['datetime','php:Y-m-d H:i']],
        ],
    ]); ?>

    <?php if(0 == $dataProvider->totalCount): ?>

        <?php if(0 == $invoices): ?>
        <p>振替依頼が必要な請求書はありません</p>

        <?php else:?>
        <p>振替依頼が必要な請求書が <?= $invoices ?> 通あります</p>
        <?= Html::a('発行する',['create','year'=>$year,'month'=>$month],['class'=>'btn btn-success','title'=>'当月の振替依頼を発行します']) ?>
        <?php endif ?>

    <?php endif ?>


    <?php if($dataProvider->totalCount && $invoices && ($invoices != $dataProvider->totalCount)): ?>
        <p class="alert alert-danger">
            重大なエラー: 
            振替依頼の総件数(<?= $dataProvider->totalCount ?>)と
            振替依頼が必要な請求書の総数(<?= $invoices ?>)が一致しません
        </p>
    <?php elseif($dataProvider->totalCount): ?>
        <?= Html::a('CSV',['export','year'=>$year,'month'=>$month],['class'=>'btn btn-danger','title'=>'当月の振替依頼をCSV形式でエクスポートします']) ?>
    <?php endif ?>

</div>
