<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/ysd/views/rrs/view.php $
 * @version $Id: view.php 1961 2016-01-11 01:39:26Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\ysd\TransferResponse
 */

$this->params['breadcrumbs'][] = ['label' => $model->trs_id];

$pkey = $model->primaryKey;

?>

<div class="transfer-response-view">

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

    <h1>振替結果</h1>

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

    <?php if($model->isPaid()): ?>
        <p class="alert alert-info">
            振替は成功しました
        </p>
    <?php else: ?>
        <p class="alert alert-danger">
            <strong>注意</strong>
            振替は成功しませんでした: <?= ($s = $model->status) ? $s->name : null ?>
        </p>
    <?php endif ?>

    <h1><small>口座情報</small></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'bankcd',
                'format'    => 'html',
                'value'     => sprintf('------ (%s)',
                                       Html::tag('span','社内DBには保持しません',['class'=>'not-set'])),
            ],
            [
                'attribute' => 'brcd',
                'format'    => 'html',
                'value'     => sprintf('------ (%s)',
                                       Html::tag('span','社内DBには保持しません',['class'=>'not-set'])),
            ],
            [
                'attribute' => 'acitem',
                'format'    => 'html',
                'value'     => sprintf('------ (%s)',
                                       Html::tag('span','社内DBには保持しません',['class'=>'not-set'])),
            ],
            [
                'attribute' => 'acno',
                'format'    => 'html',
                'value'     => sprintf('------ (%s)',
                                       Html::tag('span','社内DBには保持しません',['class'=>'not-set'])),
            ],
            'acname',
            [
                'attribute' => 'stt',
                'value'     => sprintf('%d (%s)',
                                       $model->stt,
                                       ($s = $model->status) ? $s->name : Html::tag('span','社内DBに未定義',['class'=>'not-set'])),
            ],
        ],
    ]) ?>

    <?php if($trq = $model->request): ?>
        <?= Html::a("依頼に戻る",['trq/view','id'=>$trq->trq_id]) ?>
    <?php endif ?>

</div>
