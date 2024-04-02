<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\EventVenue */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Event Venues', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-venue-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-right">
        <?= Html::a('Update', ['update', 'id' => $model->venue_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'product_id',
                'value'     => ($model->product ? $model->product->name : null),
            ],
            'venue_id',
            'name',
            [
                'attribute' => 'branch_id',
                'value'     => ($model->branch ? $model->branch->name : null),
            ],
            'event_date',
            [
                'attribute' => 'start_time',
                'value'     => Yii::$app->formatter->asTime($model->start_time,'php:H:i'),
            ],
            [
                'attribute' => 'end_time',
                'value'     => Yii::$app->formatter->asTime($model->end_time,'php:H:i'),
            ],
            [
                'attribute' => 'pub_date',
                'value'     => Yii::$app->formatter->asDate($model->pub_date,'php:Y-m-d'),
            ],
            'capacity',
            'allow_child',
            'overbook',
        ],
    ]) ?>

</div>
