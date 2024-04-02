<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use common\models\EventVenue;
use common\models\SearchProductFavor;

/**
 * @var $this yii\web\View
 * @var $model  common\models\Product
 * @var $provider yii\data\ActiveDataProvider
 */

$this->params['body_id'] = 'Product';
$this->params['breadcrumbs'][] = ['label' => 'イベント', 'url' => ['/category/viewbyname','name'=>'イベント']];
?>

<div class="product">

<div class="col-md-4 product-photo">

    <?= $this->render('_image', ['model'=>$model]) ?>

</div>

<div class="col-md-8 product-detail">

    <?= $this->render('_detail', ['model'=>$model]) ?>

<?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getVenues(),
            'sort'  => false,
        ]),
        'layout' => '{items}',
        'tableOptions' => ['class'=>'table table-condensed table-hover'],
        'columns' => [
            [
                'attribute' => 'event_date',
                'value'     => function($data){
                    return Yii::$app->formatter->asDate($data->event_date, 'php:m/d(D)');
                },
                'visible'   => (1 < $model->getVenues()->select('event_date')->distinct()->count())
            ],
            [
                'attribute' => 'start_time',
                'visible'   => (1 < $model->getVenues()->select('start_time')->distinct()->count())
            ],
            [
                'attribute' => 'end_time',
                'visible'   => (1 < $model->getVenues()->select('end_time')->distinct()->count())
            ],
            [
                'attribute' => 'name',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->name,['apply','id'=>$data->venue_id]); },
            ],
            'capacity',
            [
                'attribute'  => 'vacancy',
                'value'  => function($data){
                    if($data->occupancy <   0.5) return '◎';
                    if($data->vacancy   <= 20  ) return '▲';
                    return '○';
                },
            ],
            [
                'attribute' => 'allow_child',
                'value'     => function($data){return $data->allow_child ? '○' : '×'; }
            ],
            [
                'label'=>'',
                'format'=>'html',
                'value'=>function($data)
                {
                    return Html::a('予約',['apply','id'=>$data->venue_id],['class'=>'btn btn-warning btn-sm']);
                },
            ],
        ],
    ]) ?>

</div>
</div>
