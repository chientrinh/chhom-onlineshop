<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/ysd/views/rrs/view.php $
 * @version $Id: view.php 1961 2016-01-11 01:39:26Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\ysd\TransferRequest
 */

$this->params['breadcrumbs'][] = ['label' => $model->trq_id];

$pkey = $model->primaryKey;

?>

<div class="transfer-request-view">

    <div class="pull-right">
        <?= Html::a( Html::tag('i','',['class'=>'glyphicon glyphicon-chevron-left']), ['view','id'=>$pkey - 1 ], [
            'class'=>'btn btn-xs btn-default '.($model->findOne($pkey-1)?null:'disabled'),
            'title'=>'前'
        ]) ?>
        <?= Html::a( Html::tag('i','',['class'=>'glyphicon glyphicon-chevron-right']), ['view','id'=>$pkey + 1], [
            'class'=>'btn btn-xs btn-default '.($model->findOne($pkey+1)?null:'disabled'),
            'title'=>'次'
        ]) ?>
    </div>

    <h1>振替依頼</h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'custno',
                'format'    => 'html',
                'value'     => sprintf('%10d (%s)',
                                       $model->custno,
                                       ($c = $model->customer) ? Html::a($c->name,['/customer/view','id'=>$c->id]) : Html::tag('span','該当なし',['class'=>'not-set'])),
            ],
            'charge:currency',
            'cdate',
            [
                'attribute' => 'pre',
                'value'     => sprintf('%0d (%s)',
                                       $model->pre,
                                       ($p = $model->pre) ? 'はい' : 'いいえ'),
            ],
            'created_at:datetime',
        ],
    ]) ?>

    <?= Html::errorSummary($model,['class'=>'alert alert-danger']) ?>

    <?php if(! $trs = $model->response): ?>
        <p class="alert alert-warning">
            まだ結果はありません
        </p>
    <?php elseif($trs->isPaid()): ?>
        <p class="alert alert-info">
            振替は成功しました
        </p>
    <?php else: ?>
        <p class="alert alert-danger">
            <strong>注意</strong>
            振替は成功しませんでした: <?= ($s = $trs->status) ? $s->name : null ?>
        </p>
    <?php endif ?>

    <?php if($trs): ?>
        <?= Html::a("結果を見る",['trs/view','id'=>$trs->trs_id]) ?>
    <?php endif ?>

</div>
