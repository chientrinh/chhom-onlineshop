<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @link $URL$
 * @version $Id$
 *
 * @var $this yii\web\View
 * @var $model common\models\ysd\RegisterRequest
 */

$this->params['breadcrumbs'][] = ['label' => $model->rrq_id];

$pkey = $model->primaryKey;

?>

<div class="register-request-view">

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

    <h1>登録依頼</h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'userno',
                'format'    => 'html',
                'value'     => sprintf('%10d (%s)',
                                       $model->userno,
                                       ($c = $model->customer) ? Html::a($c->name,['/customer/view','id'=>$c->id]) : Html::tag('span','該当なし',['class'=>'not-set'])),
            ],
            'ip',
            'feedback',
            'emsg:ntext',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

    <p class="help-block">
        YSDへ送信したPOSTデータは以下の通りです
    </p>
    <?= DetailView::widget([
        'options' => ['class' =>'table table-condensed'],
        'model' => $model->postData,
        'attributes' => [
             'CORPCD'
            ,'PASSWORD'
            ,'SVCNO' 
            ,'USERNO'
            ,'SEIKANJI'
            ,'SEIKANA'
            ,'MEIKANJI'
            ,'MEIKANA'
            ,'ZIPCD' 
            ,'TELNO' 
            ,'ADDR'
            ,'BIRTHDAY'
            ,'MAILADDR'
        ],
    ]) ?>

</div>
