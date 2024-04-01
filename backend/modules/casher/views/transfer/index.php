<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseStringHelper;
use \common\models\Branch;
use \common\models\Transfer;
use \common\models\TransferStatus;
use \backend\models\Staff;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/transfer/index.php $
 * $Id: index.php 3196 2017-02-26 05:11:03Z naito $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$query = clone($dataProvider->query);
$src      = ArrayHelper::map(Branch::find()->where(['branch_id'=>
    $query->distinct()->select('src_id')
])->all(),'branch_id','name');
$dst      = ArrayHelper::map(Branch::find()->where(['branch_id'=>
    $query->distinct()->select('dst_id')
])->all(),'branch_id','name');

$status   = ArrayHelper::map(TransferStatus::find()->where(['status_id'=>
    $query->distinct()->select('status_id')
])->all(),'status_id','name');

if($branch = $this->context->module->branch)
    $branch_id = $branch->branch_id;
else
    $branch_id = null;

?>
<div class="transfer-index">

    <div class="list-group col-md-2 col-sm-3">
        <?= $this->render('/default/_menu') ?>
    </div>

    <div class="col-md-10 col-sm-9">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="pull-right">
        現在、カートの中に <?= count($model->items) ?> 品目あります
        <?= Html::a('カートを見る',['create'],['class'=> $model->items ? 'btn btn-success' : 'btn btn-default']) ?>
    </p>

    <?php $form = \yii\bootstrap\ActiveForm::begin([
        'id'    => 'casher-transfer-form',
        'action'=> 'index',
        'method'=> 'get',
    ]); ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'layout' => '{items}{pager}{summary}',
        'summaryOptions' => ['class'=>'pull-right small text-muted'],
        'columns' => [
            [
                'class' => \yii\grid\CheckboxColumn::className(),
            ],
            [
                'attribute' => 'purchase_id',
                'format'    => 'html',
                'value'     => function($data){ return Html::a(sprintf('%06d',$data->purchase_id),['view','id'=>$data->purchase_id]); },
            ],
            [
                'attribute' => 'src_id',
                'format'    => 'raw',
                'value'     => function($data){
                    if($branch = $data->src) return $branch->name;
                        return Html::tag('span',BaseStringHelper::truncate($branch->name, 7),['title'=>$branch->name]);
                },
                'filter'    => $src,
                'headerOptions' => ['class'=>'col-md-2'],
            ],
            [
                'attribute' => 'dst_id',
                'format'    => 'raw',
                'value'     => function($data)
                {
                    if($branch = $data->dst)
                    {
                        $name = preg_replace('/ホメオパシージャパンShop/','HJ', $branch->name);
                        $name = preg_replace('/ショップ/',              '',   $name);
                        return $name;
                    }
                },
                'headerOptions' => ['class'=>'col-md-2'],
                'filter'    => $dst,
            ],
            [
                'attribute' => 'asked_at',
                'format'    => ['datetime','php:Y-m-d H:i'],
                'contentOptions' => ['class'=>'text-nowrap'],
            ],
            [
                'attribute' => 'posted_at',
                'format'    => ['datetime','php:Y-m-d H:i'],
                'contentOptions' => ['class'=>'text-nowrap'],
            ],
            [
                'attribute' => 'got_at',
                'format'    => ['datetime','php:Y-m-d H:i'],
                'contentOptions' => ['class'=>'text-nowrap'],
            ],
            [
                'attribute' => 'status_id',
                'value'     => function($data){ return $data->stat ? $data->stat->name : null ; },
                'filter'    => $status,
            ],
        ],
    ]); ?>

    <?php if($dataProvider->totalCount < Transfer::find()->active()->count()): ?>
    <p class="pull-right">
        <?= Html::a('全店表示',['index','branch'=>'all'],['class'=>'btn btn-default','title'=>'すべての移動を表示します']) ?>
    </p>
    <?php endif ?>

    <p>
        <?= Html::hiddenInput('basename', \backend\modules\casher\Module::getPrintBasename()) ?>
        <?= Html::submitButton("納品書",[
            'class'    => 'btn btn-warning',
            'title'    => '納品書をダウンロードし、状態を「配送中」にします',
            'name'     => 'target',
            'value'    => 'default',
            'onClick'  => "this.form.action='print-batch'",
        ]) ?>
    </p>

    <p>
        <?= Html::submitButton("ラベル",[
            'class'    => 'btn btn-info',
            'title'    => '滴下レメディーのラベルをダウンロードします',
            'name'     => 'target',
            'value'    => 'remedy',
            'onClick'  => "this.form.action='print-label'",
        ]) ?>
    </p>

    <p>
        <?= Html::hiddenInput('basename', \backend\modules\casher\Module::getPrintBasename()) ?>
        <?= Html::submitButton("CSV",[
            'class'    => 'btn btn-default',
            'title'    => 'ヤマトCSVを表示します',
            'name'     => 'target',
            'value'    => 'default',
            'onClick'  => "this.form.action='print-csv'",
        ]) ?>
    </p>

    <?php $form->end() ?>

    </div>

</div>
