<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/homoeopath/view.php $
 * $Id: view.php 4143 2019-03-28 08:43:17Z kawai $
 *
 * $this \yii\web\View
 * @var $model common\models\sodan\Homoeopath
 */

$this->params['breadcrumbs'][] = ['label'=>$model->customer->homoeopathname];
$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels); $labels[] = Yii::$app->name;

$this->title = implode(' | ', $labels);
?>
<div class="homoeopath-view">

    <p class="pull-right">
        <?= Html::a('修正', ['update', 'id' => $model->homoeopath_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <h1><?= $model->customer->homoeopathname ?></h1>

    <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'homoeopath_id',
                'value'     => $model->customer->homoeopathname,
            ],
            [
                'attribute' => 'branch_id',
                'format'    => 'raw',
                'value'     => $model->multibranchname
            ],
            'schedule:ntext',
            [
                'attribute' => 'del_flg',
                'value'     => ($model->del_flg) ? '無効' : '有効'
            ],
        ],
    ]) ?>

    <p class="pull-right">
        <?= Html::a('カレンダー表示',['calendar/index','hpath_id' => $model->homoeopath_id, 'branch_id' => $model->branch_id]) ?>
    </p>
    <h2>
        <small>相談会</small>
    </h2>
    <?= \common\modules\sodan\widgets\InterviewGrid::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getInterviews(),
            'sort' => ['defaultOrder'=> ['itv_date'=>SORT_DESC,'itv_time'=>SORT_DESC]]
        ]),
        'attributes'   => ['itv_id','branch_id','itv_date','itv_time','client_id','product_id','status_id'],
    ]); ?>
    <div class="row">
    <div class="col-md-6 col-xs-6">
    <h2>
        <small>クライアント</small>
    </h2>
    <?= yii\grid\GridView::widget([
        'dataProvider' => new yii\data\ActiveDataProvider([
            'query' => \common\models\sodan\Client::find()->active()->andWhere(['homoeopath_id' => $model->homoeopath_id])
        ]),
        'columns' => [
            [
                'attribute'=> 'client_id',
                'format'   => 'html',
                'value'    => function($data)
                {
                    $icon = '';
                    if($data->isAnimal())
                        $icon = Html::img(Url::base() . '/img/paw.png', ['class' => 'icon', 'title' => '動物です']);
                    elseif($data->customer->age < 13)
                        $icon = Html::tag('i', '', ['title' => '子供です', 'style' => 'color:#FF33FF', 'class' => 'glyphicon glyphicon-user']);

                    $ng_icon = (!$data->ng_flg) ? Html::tag('i', '', ['title' => '公開OKです', 'style' => 'color:#337ab7;font-size:1.2em;', 'class' => 'glyphicon glyphicon-thumbs-up']) : '';
                    $name = Html::a($data->name, ['client/view','id' => $data->client_id]);
                    return "{$icon} {$name} {$ng_icon}";
                },
            ],
        ]
    ]); ?>
    </div>

    <div class="col-md-6 col-xs-6">
    <h2><small>休業日</small></h2>
    <?= \common\modules\sodan\widgets\InterviewGrid::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getHolidays(),
            'sort' => ['defaultOrder'=> ['date'=>SORT_DESC]]
        ]),
        'attributes'   => [
            [
                'attribute' => 'id',
                'format'    => 'raw',
                'value'     => function($data){
                    return Html::a($data->id, ['calendar/holiday-setting', 'id' => $data->id]);
                }
            ],
            [
                'attribute' => 'title',
            ],
            [
                'attribute' => 'date',
                'format'    => ['date', 'php:Y-m-d D'],
            ],
            [
                'attribute' => 'all_day',
                'value'     => function($data) {
                    return $data->all_day ? '終日' : '';
                },
            ],
            [
                'attribute' => 'start_time',
                'value'     => function($data){
                    return date('H:i', strtotime($data->start_time));
                },
            ],
            [
                'attribute' => 'end_time',
                'value'     => function($data){
                    return date('H:i', strtotime($data->end_time));
                },
            ],
        ],
    ]); ?>
    <?= Html::a('追加',['calendar/holiday-setting', 'hpath_id' => $model->homoeopath_id],['class'=>'btn btn-sm btn-primary']) ?>
    </div>
    <div class="col-md-6 col-xs-6">
        <h2><small>公開枠時間帯</small></h2>
        <?= \common\modules\sodan\widgets\InterviewGrid::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query' => $model->getOpenTime(),
                'sort' => ['defaultOrder'=> ['create_date' => SORT_DESC]]
            ]),
            'attributes'   => [
                [
                    'attribute' => 'week_day',
                    'value'     => function($data) {
                        $week = [
                          '日', //0
                          '月', //1
                          '火', //2
                          '水', //3
                          '木', //4
                          '金', //5
                          '土', //6
                        ];
                        return $week[$data->week_day];
                    }
                ],
                [
                    'attribute' => 'start_time',
                    'value'     => function ($data) {
                        return $data->start_time;
                    }
                ],
                [
                    'attribute' => 'end_time',
                    'value'     => function ($data) {
                        return $data->end_time;
                    }
                ],
                [
                    'attribute' => 'delete',
                    'label'     => '',
                    'format'    => 'raw',
                    'value'     => function ($data) {
                        return Html::a('削除', ['delete-opentime', 'id' => $data->id], ['class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => '本当に削除してもいいですか',
                                'method'  => 'POST'
                            ],]);
                    }
                ]
            ],
        ]); ?>
        <?= Html::a('追加', ['add-opentime', 'hpath_id' => $model->homoeopath_id], ['class' => 'btn btn-sm btn-primary']) ?>
    </div>
    </div>

</div>
