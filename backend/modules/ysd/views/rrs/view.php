<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/ysd/views/rrs/view.php $
 * @version $Id: view.php 2254 2016-03-17 04:22:28Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\ysd\RegisterResponse
 */

$this->params['breadcrumbs'][] = ['label' => $model->rrs_id];

$pkey = $model->primaryKey;

?>

<div class="register-response-view">

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

    <h1>登録結果</h1>

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
            [
                'attribute' => 'cdate',
                'format'    => 'date',
            ],
            'custkana',
            'custname',
            'errcd',

            'created_at:datetime',
            'creator.name',
        ],
    ]) ?>

</div>
