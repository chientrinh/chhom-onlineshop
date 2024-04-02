<?php

use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/stat/detail-grid.php $
 * $Id: detail-grid.php 3851 2018-04-24 09:07:27Z mori $
 *
 * $this \yii\web\View
 * $dataProvider
 */

$dataProvider->query->select = [
    'b.name as branch',
    'CASE WHEN h.homoeopath_name = "" OR h.homoeopath_name IS NULL THEN concat(h.name01," ",h.name02) ELSE h.homoeopath_name END as hpath',
    'concat(l.name01,l.name02) as client',
    'i.itv_id',
    'i.itv_date',
    'i.itv_time',
    's.name as status',
    'p.purchase_id',
    'p.subtotal',
    'c.commision_id',
    'c.fee',
];

$fmt = Yii::$app->formatter;

?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'showFooter'   => true,
    'columns'      => [
        [
            'class' => \yii\grid\SerialColumn::className(),
        ],
        [
            'attribute' => 'branch',
            'label'     => $model->getAttributeLabel('branch_id'),
        ],
        [
            'attribute' => 'hpath',
            'label'     => $model->getAttributeLabel('homoeopath_id'),
        ],
        [
            'attribute' => 'itv_date',
            'label'     => "日時",
            'format'    => 'html',
            'value'     => function($data){
                $itv_id   = ArrayHelper::getValue($data,'itv_id');
                $itv_date = ArrayHelper::getValue($data,'itv_date');
                $itv_time = ArrayHelper::getValue($data,'itv_time');
                return Html::a($itv_date . Yii::$app->formatter->asDate($itv_time, 'php: H:i'),
                               ['interview/view','id'=>$itv_id],['class'=>'btn-default']);
            },
        ],
        [
            'attribute' => 'client',
            'label'     => "クライアント",
        ],
        [
            'attribute' => 'status',
        ],
        [
            'attribute' => 'subtotal',
            'label'     => "売上 (小計)",
            'format'    => 'html',
            'value'     => function($data){
                $subtotal    = ArrayHelper::getValue($data,'subtotal');
                $purchase_id = ArrayHelper::getValue($data,'purchase_id');
                return Html::a(Yii::$app->formatter->asCurrency($subtotal),['/purchase/view','id'=>$purchase_id],['class'=>'btn-default']);
            },
            'footer'         => $fmt->asCurrency(array_sum(ArrayHelper::getColumn($dataProvider->models, 'subtotal'))),
            'contentOptions' => ['class'=>'text-right'],
            'footerOptions'  => ['class'=>'text-right'],
        ],
        [
            'attribute' => 'commission',
            'label'     => $model->getAttributeLabel('commission'),
            'format'    => 'html',
            'value'     => function($data){
                $fee    = ArrayHelper::getValue($data,'fee');
                $cid    = ArrayHelper::getValue($data,'commision_id');
                return Html::a(Yii::$app->formatter->asCurrency($fee),['/commission/view','id'=>$cid],['class'=>'btn-default']);
            },
            'footer'         => $fmt->asCurrency(array_sum(ArrayHelper::getColumn($dataProvider->models, 'fee'))),
            'contentOptions' => ['class'=>'text-right'],
            'footerOptions'  => ['class'=>'text-right'],
        ],
    ],
]) ?>
