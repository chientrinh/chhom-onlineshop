<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EventVenue */

$this->title = 'Create Event Venue';
$this->params['breadcrumbs'][] = ['label' => 'Event Venues', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-venue-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
