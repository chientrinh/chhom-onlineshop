<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EventVenue */

$this->title = 'Update Event Venue: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Event Venues', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->venue_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="event-venue-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
