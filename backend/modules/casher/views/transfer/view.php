<?php

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/transfer/view.php $
 * $Id: view.php 3196 2017-02-26 05:11:03Z naito $
 *
 * @var $this yii\web\View
 * @var $model common\models\Transfer
 */

use yii\helpers\Html;
use common\models\TransferStatus;

$this->title = $model->purchase_id;
$this->params['breadcrumbs'][] = ['label' => sprintf('%06d',$model->purchase_id),'url' => ['view','id'=>$model->purchase_id]];

$jscode = "
$('#toggle-btn').click(function(){
     {
         $('#sub-menu').toggle();
     }
 	return true;
});
$('textarea').change(function(){
     {
         $(this).submit();
         return false;
     }
 	return true;
});
$('input').change(function(){
    $(this).submit();
    return false;
});
";
$this->registerJs($jscode);

if('update' == $model->scenario)
    $enableUpdateLink = false;
elseif($model->isExpired() || (TransferStatus::PKEY_DONE <= $model->status_id))
    $enableUpdateLink = false;
else
    $enableUpdateLink = true;

?>
<div class="purchase-view">

    <div class="pull-right">
        <?php if($model->prev): ?>
        <?= Html::a( Html::tag('i','',['class'=>'glyphicon glyphicon-chevron-left']), ['view','id'=>$model->prev->purchase_id], ['class'=>'btn btn-xs btn-default']) ?>
        <?php endif ?>
        <?php if($model->next): ?>
        <?= Html::a( Html::tag('i','',['class'=>'glyphicon glyphicon-chevron-right']), ['view','id'=>$model->next->purchase_id], ['class'=>'btn btn-xs btn-default','title'=>'次']) ?>
    <?php endif ?>
    </div>
    <h1><?= $model->getAttributeLabel('purchase_id') ?> : <?= sprintf('%06d',$model->purchase_id) ?></h1>

    <p class="pull-right">
    <?php if('casher' == $this->context->module->id): ?>
        <?= Html::a('再出荷', ['duplicate', 'id' => $model->purchase_id], ['class' => 'btn btn-default']) ?>
        <?= Html::a('修正', ['view', 'id' => $model->purchase_id, 'scenario'=>'update'], ['class' => 'btn btn-primary' . ($enableUpdateLink ?'':' disabled')]) ?>
    <?php endif ?>
        <?= Html::a('キャンセル', ['cancel', 'id' => $model->purchase_id], [
            'class' => 'btn btn-danger'.('update'==$model->scenario?' disabled':''),
            'data' => [
                'confirm' => '本当にキャンセルしていいですか？',
                'method'  => 'post',
            ],
        ]) ?>
    </p>

    <div>
        <?= Html::a('納品書',
                    ['print', 'id' => $model->purchase_id, 'format'=>'pdf'],
                    ['class' => 'btn btn-default']) ?>

        <?= Html::a('レシート', ['receipt', 'id' => $model->purchase_id], [
            'class' => 'btn btn-default',
            'style' => $model->isExpired() ? 'background:lightgray' : null ,
        ]) ?>

        <?= Html::a('CSV',
                    ['print-csv', 'id' => $model->purchase_id],
                    ['class' => 'btn btn-default',
                     'title' => $model->posted_at ? '出荷済のためCSVは不要です' : 'ヤマト便のためにCSVを出力します',
                     'style' => $model->posted_at ? 'background:lightgray' : null ,
                    ]) ?>

        <div class="btn-group">
            <?= Html::a('荷札',
                        ['print-label','id' => $model->purchase_id, 'target'=>'sticker'],
                        ['class' => 'btn btn-default',
                         'title' => '大口顧客向けにバーコードシールを出力します',
                         'style' => $model->isExpired() ? 'background:lightgray' : null ,
                        ]) ?>
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li role="presentation"><?= Html::a('値札',['print-label', 'id' => $model->purchase_id, 'format'=>'pdf', 'target'=>'price'],['title'=>'店頭に陳列するための値札を出力します','role'=>"menuitem"]) ?></li>
                <li role="presentation">
                    <?= Html::a('レメディーラベル',
                                ['print-label', 'id' => $model->purchase_id],
                                ['title' => '滴下レメディーのラベルを出力します',
                                 'style' => $model->isExpired() ? 'background:lightgray' : null ,
                                 'role'  => "menuitem"]) ?>
                </li>
            </ul>
        </div>

    </div>

    <?php if($model->isExpired()): ?>
        <p class="alert alert-danger">
            この伝票は無効です。
        </p>
    <?php endif ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels'  => $model->items,
            'pagination' => false,
        ]),
        'layout'  => '{items}',
        'columns' => [
            'code',
            [
                'attribute' => 'name',
                'format'    => 'html',
                'value'     => function($data){
                    if($data->product_id)
                        return Html::a($data->name,['/product/view','id'=>$data->product_id]);
                    if($data->remedy_id)
                        return Html::a($data->name,['/remedy/view','id'=>$data->remedy_id]);
                    return $data->name;
                }
            ],
            [
                'attribute' => 'qty_request',
                'format'    => 'html',
                'value'     => function($data){
                    if($data->qty_request != $data->qty_shipped)
                        return Html::tag('del',$data->qty_request);
                    else
                        return $data->qty_request;
                },
                'visible'   => ('update' === $model->scenario),
                'contentOptions' => ['class'=>'text-center'],
            ],
            [
                'attribute' => 'qty_shipped',
                'format'    => 'raw',
                'value'     => function($data, $key, $index, $column){
                    return $this->render('_update', ['model'=>$data]);
                },
                'visible'   => ('update' === $model->scenario),
                'headerOptions' => ['class'=>'col-md-1 col-xs-1'],
            ],
            [
                'attribute' => 'quantity',
                'format'    => 'integer',
                'visible'   => ('update' !== $model->scenario),
                'headerOptions' => ['class'=>'col-md-1 col-xs-1'],
                'contentOptions' => ['class'=>'text-center'],
            ],
            [
                'attribute' => 'price',
                'format'    => 'currency',
                'contentOptions' => ['class'=>'text-right'],
            ],
            [
                'attribute' => 'charge',
                'format'    => 'currency',
                'contentOptions' => ['class'=>'text-right'],
            ],
        ],
    ]) ?>

    <strong><?= $model->getAttributeLabel('delivery') ?></strong>
    <?= \yii\widgets\DetailView::widget([
            'model'   => $model->delivery,
            'options' => ['class' => 'table table-condensed table-bordered detail-view text-right'],
            'attributes' => [
                [
                    'attribute' => 'addr',
                    'value'     => sprintf('〒%s %s', $model->delivery->zip, $model->delivery->addr),
                ],
                'name',
                'tel',
            ],
        ]) ?>

    <div class="well">
        <strong><?= $model->getAttributeLabel('note') ?></strong>
        <?= Html::encode(strlen($model->note) ? $model->note : "(なし)") ?>

        <?php if('update' === $model->scenario): ?>

            <?php $form = \yii\bootstrap\ActiveForm::begin([
                'action' => ['update'],
                'method' => 'get',
            ]) ?>
            <?= Html::hiddenInput('id',$model->purchase_id) ?>
            <?= Html::hiddenInput('target','note') ?>
            <?= Html::textArea('value',$model->note,['class'=>'form-control']) ?>
            <?= Html::submitButton('保存') ?>
            <?php $form->end() ?>

        <?php else: ?>

        <?php endif ?>
    </div>

    <div class="row">

    <div class="col-md-6" style="float:right">
    <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'options' => ['class'=>'table table-condensed'],
        'attributes' => [
            [
                'attribute' => 'create_date',
                'format'    => 'html',
                'value'     => $model->create_date . ($model->creator ? Html::a(sprintf(' (%s)',$model->creator->name01), ['/staff/view', 'id'=>$model->created_by]) : null),
            ],
            [
                'attribute' => 'update_date',
                'format'    => 'html',
                'value'     => $model->update_date . ($model->updator ? Html::a(sprintf(' (%s)',$model->updator->name01), ['/staff/view', 'id'=>$model->updated_by]) : null),
            ],
        ],
    ]) ?>
    </div>

    <div class="col-md-6">
    <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'src_id',
                'format'    => 'html',
                'value'     => $model->src ? $model->src->name : null,
            ],
            [
                'attribute' => 'dst_id',
                'format'    => 'html',
                'value'     => $model->dst ? $model->dst->name : null,
            ],
            [
                'attribute' => 'asked_at',
                'format'    => 'datetime',
            ],
            [
                'attribute' => 'posted_at',
                'format'    => 'datetime',
                'visible'   => isset($model->posted_at),
            ],
            [
                'attribute' => 'posted_at',
                'format'    => 'html',
                'value'     => Html::a('配送中にする',['update','target'=>'status_id','id'=>$model->purchase_id,'value'=>TransferStatus::PKEY_POSTED],['class'=>'btn btn-sm btn-success']),
                'visible'   => ! isset($model->posted_at),
            ],
            [
                'attribute' => 'got_at',
                'format'    => 'datetime',
                'visible'   => isset($model->got_at),
            ],
            [
                'attribute' => 'got_at',
                'format'    => 'html',
                'value'     => Html::a('検収済みにする',['update','target'=>'status_id','id'=>$model->purchase_id,'value'=>TransferStatus::PKEY_RECEIVED],['class'=>'btn btn-sm btn-success']),
                'visible'   => isset($model->posted_at) && ! isset($model->got_at),
            ],
            [
                'attribute' => 'status_id',
                'format'    => 'raw',
                'value'     => ($model->isExpired() && $model->stat)
                    ? Html::tag('p', $model->stat->name, ['class'=>'alert-text alert-danger'])
                    : $model->stat->name . Html::button('変更',['id'=>'toggle-btn','class'=>'btn btn-xs btn-default']),
            ],
        ],
    ]) ?>

        <div id="sub-menu" style="display:none" class="alert alert-info col-md-12">

            <?= \yii\widgets\ListView::widget([
                'dataProvider' => new \yii\data\ActiveDataProvider([
                    'query' => TransferStatus::find()->where(['>','status_id',$model->status_id])
                                                     ->andWhere(['<','status_id',TransferStatus::PKEY_CANCEL]),
                ]),
                'layout' => '{items}',
                'emptyText' => '変更できません',
                'itemView' => function ($data, $key, $index, $widget) use ($model)
                {
                    $disabled = ($data->status_id <= $model->status_id) ? true : false;

                    return Html::a($data->name,
                                   ['update','id'=>$model->purchase_id,'target'=>'status_id','value'=>$data->status_id],
                                   ['class'=>'btn btn-default'.($disabled ? ' disabled' : '')]
                    );
                }
            ]) ?>

        </div>

    </div>

    </div>

    <div class="row">
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels'  => $model->mails,
        ]),
        'tableOptions' => ['class'=>'table table-condensed'],
        'layout'  => '{items}',
        'caption' => 'メール送信履歴',
        'columns' => [
            [
                'attribute' => 'date',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->date, ['/mail-log/view', 'id'=>$data->mailer_id]); },
            ],
            'subject',
        ],

    ]) ?>

    </div>

</div>
