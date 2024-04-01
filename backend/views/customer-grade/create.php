<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CustomerGrade */

$this->title = 'Create Customer Grade';
$this->params['breadcrumbs'][] = ['label' => 'Customer Grades', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-grade-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
