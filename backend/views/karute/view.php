<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/karute/view.php $
 * $Id: view.php 2276 2016-03-20 06:58:20Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\webdb20\Karute
 */

$this->title = sprintf('ID %s | %s | %s', $model->karuteid, 'カルテ', Yii::$app->name);

?>
<div class="karute-view">

    <div class="pull-right">

        <?php if($model->prev): ?>
        <?= Html::a( Html::tag('i','',['class'=>'glyphicon glyphicon-chevron-left']), ['view','id'=>$model->prev->karuteid], ['class'=>'btn btn-xs btn-default']) ?>
        <?php endif ?>
        <?php if($model->next): ?>
        <?= Html::a( Html::tag('i','',['class'=>'glyphicon glyphicon-chevron-right']), ['view','id'=>$model->next->karuteid], ['class'=>'btn btn-xs btn-default','title'=>'次']) ?>
        <?php endif ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'karuteid',
            [
                'attribute' => 'customerid',
                'format'    => 'raw',
                'value'     => ! $model->customer ? null : $model->customer->name . '&nbsp;' . Html::a('webdb20',sprintf('https://webdb20.homoeopathy.co.jp/index.php?m=search&out_html=customer_dsp&customerid=%d',$model->customerid),['target'=>'webdb20','class'=>'glyphicon glyphicon-log-out']),
            ],
            'karute_date:ntext',
            'karute_syuso:ntext',
            [
                'attribute' => 'syoho_homeopathid',
                'value'     => ! $model->homoeopath ? null : $model->homoeopath->name,
            ],
            'karute_fax_data:ntext',
        ],
    ]) ?>

    <p class="pull-right">
        <?= Html::a('修正', ['update', 'id' => $model->karuteid], ['class' => 'btn btn-primary']) ?>
    </p>

<?= \yii\grid\GridView::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => $model->getItems(),
        'sort'  => [
            'defaultOrder' => ['syoho_date' => SORT_DESC],
        ],
    ]),
    'caption' => '子カルテ',
    'layout'  => '{items}{pager}{summary}',
    'columns' => [
        [
            'attribute' =>'syohoid',
            'format'    => 'html',
            'value'     => function($data){ return Html::a($data->syohoid, ['karute-item/view','id'=>$data->syohoid]); },
        ],
        'syoho_date',
        [
            'attribute' => 'syoho_homeopathid',
            'value'     => function($data){ if($data->homoeopath) return $data->homoeopath->name; },
        ],
        [
            'attribute' => 'syoho_coment',
            'value'     => function($data){ return \yii\helpers\StringHelper::truncate($data->syoho_coment, 48); },
        ],
        [
            'attribute' => 'syoho_advice',
            'value'     => function($data){ return \yii\helpers\StringHelper::truncate($data->syoho_advice, 48); },
        ],
        [
            'attribute' => 'user_report',
            'value'     => function($data){ return \yii\helpers\StringHelper::truncate($data->user_report, 48); },
        ],
    ],
]) ?>

    <?= Html::a('総カルテ',['print','id'=>$model->karuteid],['class'=>'btn btn-default']) ?>

</div>
