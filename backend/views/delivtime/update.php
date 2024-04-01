<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/delivtime/update.php $
 * $Id: update.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DelivTime */

$this->title = 'Update Deliv Time: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Deliv Times', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->dtime_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="deliv-time-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
