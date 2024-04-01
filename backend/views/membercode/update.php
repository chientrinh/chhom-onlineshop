<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Membercode */

$this->title = 'Update 会員証NO: ' . ' ' . $model->code;
$this->params['breadcrumbs'][] = ['label' => 'Membercodes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->code, 'url' => ['view', 'id' => $model->code]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="membercode-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
