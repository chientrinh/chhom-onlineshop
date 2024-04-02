<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Event Venues';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-venue-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'venue_id',
            [
                'attribute' => 'product_id',
                'value'     => function($data)
                {
                    return $data->product ? $data->product->name : ($data->client_id ? null : ''); 
                },
            ],
            'name',
            [
                'attribute' => 'branch_id',
                'value'     => function($data)
                {
                    if($data->branch) return preg_replace('/日本ホメオパシーセンター|総?本部/u', '', $data->branch->name); 
                },
            ],
            'event_date',
            [
                'attribute' => 'start_time',
                'value'     => function($data)
                {
                    if($data->start_time)
                    {
                        return date('H:i', strtotime($data->start_time));
                    }
                },
            ],
            [
                'attribute' => 'end_time',
                'value'     => function($data)
                {
                    if($data->end_time)
                    {
                        return date('H:i', strtotime($data->end_time));
                    }
                },
            ],
            [
                'attribute' => 'pub_date',
                'value'     => function($data)
                {
                    if($data->pub_date)
                    {
                        return Yii::$app->formatter->asDate($data->pub_date,'php:Y-m-d');
                    }
                },
            ],

            'capacity',
            'allow_child',
            'overbook',
            [
                'label' => '大人',
                'value' => function($data){ return (int) $data->getAttendees()->sum('adult'); }
            ],
            [
                'label' => '小人',
                'value' => function($data){ return (int) $data->getAttendees()->sum('child'); }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <p>
        <?= Html::a('Create Event Venue', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

</div>
