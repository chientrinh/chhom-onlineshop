<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Membercode */

$this->title = $model->code;
$this->params['breadcrumbs'][] = ['label' => '会員証NO', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

if(in_array($model->directive,['webdb18','webdb20']))
$outerUrl = Html::a($model->directive,
                        sprintf('https://%s.homoeopathy.co.jp/index.php?m=search&out_html=customer_dsp&customerid=%d',
                                $model->directive,
                                $model->migrate_id),
                        [
                            'class' => 'glyphicon glyphicon-log-out',
    ]);
else
    $outerUrl = '';

?>
<div class="membercode-view">

    <div class="pull-right">
        <?php if($model->prev): ?>
        <?= Html::a('', ['view','id'=>$model->prev->code], ['class'=>'btn btn-xs btn-default glyphicon glyphicon-chevron-left','title'=>sprintf('前:%s',$model->prev->code)]) ?>
        <?php endif ?>
        <?php if($model->next): ?>
        <?= Html::a('', ['view','id'=>$model->next->code], ['class'=>'btn btn-xs btn-default glyphicon glyphicon-chevron-right','title'=>sprintf('次:%s',$model->next->code)]) ?>
        <?php endif ?>
    </div>

    <h1><?= Html::encode($this->title) ?></h1>

    <!-- <p>
        <?= Html::a('Update', ['update', 'id' => $model->code], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->code], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p> -->

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'code',
            'barcode',
            'status',
            [
                'attribute' => 'customer_id',
                'format'    => 'html',
                'value'     => Html::a($model->customer_id, ['/customer/view','id'=>$model->customer_id],['title'=>$model->customer ? $model->customer->name : '']),
            ],
            'pw',
            'directive',
            [
                'attribute' => 'migrate_id',
                'format'    => 'html',
                'value'     => ($model->migrate_id ? Html::a($model->migrate_id, ['/webdb/customer/view','id'=>$model->migrate_id,'db'=>$model->directive]) : '') . '&nbsp;' . $outerUrl,

            ],
            'update_date',
        ],
    ]) ?>

    <?php if($model->customer): ?>
        <p class="help-block">
            移行は完了しています <?= Html::a('詳細を表示',['/customer/view','id'=>$model->customer_id]) ?>
        </p>
    <?php else: ?>
        <div class="form-group">
            <?= Html::a('移行する',['/customer/migrate','from'=>$model->directive,'id'=>$model->migrate_id,],['class'=>'btn btn-warning','title'=>'WEBDBからえびすへ、会員情報を移行します。ご本人の同意を得たうえで操作してください']) ?>
        </div>
    <?php endif ?>


    <h1><small>DB操作履歴</small></h1>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => \common\models\ChangeLog::find()->where(['tbl' => $model->tableName(),
                                                 'pkey'=> $model->code]),
        ]),
        'columns' => [
            [
                'attribute' => 'create_date',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->create_date, ['/change-log/view','id'=>$data->create_date]); },
            ],
            'route',
            'action',
            'before',
            'after',
        ],
    ]) ?>

</div>
