<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/__customer.php $
 * $Id: __customer.php 2709 2016-07-14 02:26:14Z mori $
 * 
 * @param $model Customer
 */

use \yii\helpers\Html;

$action = $this->context->action;
$target = Yii::$app->request->get('target');

if('atami' == $this->context->id)
    $validateAttr = ['name01','name02','kana01','kana02','zip01','zip02','pref_id','addr01','addr02','tel01','tel02','tel03'];
else
    $validateAttr = ['kana01','kana02','tel01','tel02','tel03'];
?>

<div class="well">
    <p class="pull-right">
        <?php if($model): ?>
        <?= Html::a('',['apply','target'=>'customer','id'=>0],['class'=>'glyphicon glyphicon-remove','style'=>'color:#999','title'=>'削除します']) ?>
    <?php endif ?>
    </p>
    <p>
        お客様
        <?= Html::a('',['search','target'=>'customer'],['class'=>'glyphicon glyphicon-user','style'=>'color:#999','title'=>'お客様を検索します']) ?>
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
                'attribute' => 'name',
                'format'    => 'html',
                'value'     => Html::a($model->name, ['/customer/view','id'=>$model->customer_id]),
            ],
            [
                'attribute' => 'point',
                'format'    => 'html',
                'value'     => Html::a(number_format($model->point), ['apply','target'=>'summary','point_consume'=>$model->point]),
            ],
            [
                'attribute' => 'grade.name',
                'label'     => $model->getAttributeLabel('grade'),
                'format'    => 'text',
            ],
            [
                'attribute' => 'memberships',
                'label'     => $model->getAttributeLabel('membership'),
                'format'    => 'html',
                'value'     => implode('<br>',\yii\helpers\ArrayHelper::getColumn($model->memberships, 'name')),
            ],
            [
                'attribute' => 'code',
                'format'    => 'html',
                'value'     => ((($c = $model->membercode) && $c->isVirtual()) ? Html::tag('strong','会員証は未発行です',['class'=>'text-danger']) : '') .'&nbsp;'. $model->code,
            ],
        ]
    ]) ?>        

    <?php $model->validate($validateAttr); echo \yii\bootstrap\ActiveForm::begin()->errorSummary($model,['header'=>'お客様の登録が完了していません。以下の項目を修正してください','footer'=>Html::a('修正する',['/customer/update','id'=>$model->customer_id,'scenario'=>'emergency'],['class'=>'btn btn-warning'])]); \yii\bootstrap\ActiveForm::end(); ?>

    <?php endif ?>

</div>
