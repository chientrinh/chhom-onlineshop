<?php 
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/sodan/views/karute/view.php $
 * $Id: view.php 1638 2015-10-11 14:40:16Z mori $
 */

use \yii\helpers\Html;

$this->title = sprintf('総カルテ | ID:%d | カルテ | 健康相談', $model->karuteid);
?>

<div class="karute-view">

    <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            'karuteid',
            [
                'attribute' => 'customerid',
                'format'    => 'raw',
                'value'     => ! $model->customer ? null : $model->customer->name . '&nbsp;' . Html::a('webdb21',sprintf('https://webdb21.homoeopathy.co.jp/index.php?m=search&out_html=customer_dsp&customerid=%d',$model->customerid),['target'=>'webdb21','class'=>'glyphicon glyphicon-log-out']),
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
            'value'     => function($data){ return Html::a($data->syohoid, ['/sodan/karute/view','id'=>$data->syohoid,'target'=>'item']); },
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
        [
            'label'     => '',
            'format'    => 'html',
            'value'     => function($data){ return Html::a('修正', ['update','id'=>$data->syohoid,'target'=>'item'], ['class'=>'btn btn-xs btn-default']); }
        ],
    ],
]) ?>

    <?= Html::a('総カルテ',['view','id'=>$model->karuteid,'target'=>'print'],['class'=>'btn btn-default']) ?>
</div>
