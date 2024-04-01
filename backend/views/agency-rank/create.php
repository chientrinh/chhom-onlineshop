<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CustomerGrade */

$this->title = 'HJ代理店割引率追加';
$this->params['breadcrumbs'][] = ['label' => 'HJ代理店割引率', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-grade-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
