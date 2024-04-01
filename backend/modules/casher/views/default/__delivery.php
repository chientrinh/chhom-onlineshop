<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/__delivery.php $
 * $Id: __delivery.php 3704 2017-10-25 10:34:12Z kawai $
 * 
 * @param $model    PurchaseDelivery
 * @param $cusotmer Customer
 */

use \yii\helpers\Html;

$action = $this->context->action;
$target = Yii::$app->request->get('target');

?>

<div class="well" style="background-color:white">
    <p class="pull-right">
        <?php if($model): ?>
        <?= Html::a('',['update','id'=>0,'target'=>'delivery'],['class'=>'glyphicon glyphicon-pencil','title'=>'修正します']) ?>
    <?php endif ?>
    </p>
    <p>
        お届け先
        <?php if($customer): ?>
        <?= Html::a('',['search','target'=>'delivery'],['class'=>'glyphicon glyphicon-tags','style'=>'color:#999','title'=>'お客様の住所録から検索します']) ?>
        <?php endif ?>
    </p>
    <p>
    </p>
    <?php if(! $model): ?>
    <?php else: ?>
    <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table table-condensed text-right',
                      'id'    => 'customer-detail',
        ],
        'attributes' => [
            [
                'attribute' => 'addr',
                'format'    => 'html',
                'value'     => Html::tag('p','〒'.$model->zip) . Html::tag('p',$model->addr) 
            ],
            [
                'attribute' => 'name',
                'format'    => 'html',
                'value'     => sprintf('%s (%s)', $model->name, $model->kana),
            ],
            [
                'attribute' => 'tel',
            ],
            [
                'attribute' => 'expect_date',
                'format'    => 'html',
                'value'     => $model->dateTimeString
                             . '&nbsp;'
                             . Html::a('',['update','id'=>0,'target'=>'delivery'],['class'=>'glyphicon glyphicon-pencil','title'=>'修正します'])
            ],
            [
                'attribute' => 'gift',
                'value'     => $model->gift ? '非表示' : '表示',
            ],
        ]
    ]) ?>        
    <?php endif ?>

    <?php $model->validate(['name01','name02','kana01','kana02','zip01','zip02','pref_id','addr01','addr02','tel01','tel02','tel03']); echo \yii\bootstrap\ActiveForm::begin()->errorSummary($model,['footer'=>Html::a('修正する',['update','id'=>0,'target'=>'delivery'],['class'=>'btn btn-warning'])]); \yii\bootstrap\ActiveForm::end(); ?>

</div>
