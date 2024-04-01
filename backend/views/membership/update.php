<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Membership */

$this->title = 'Update Membership: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Memberships', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->membership_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="membership-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
