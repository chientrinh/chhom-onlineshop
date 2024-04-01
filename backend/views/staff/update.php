<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/staff/update.php $
 * $Id: update.php 895 2015-04-17 00:40:58Z mori $
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Staff */

$this->title = sprintf("従業員 #%d: %s", $model->staff_id, $model->name);
$this->params['breadcrumbs'][] = ['label' => "従業員", 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->staff_id, 'url' => ['view', 'id' => $model->staff_id]];
$this->params['breadcrumbs'][] = "編集";
?>
<div class="staff-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
