<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CustomerGrade */

$this->title = 'Update Customer Grade: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Customer Grades', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->grade_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="customer-grade-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
