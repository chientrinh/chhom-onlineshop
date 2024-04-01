<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Membership */

$this->title = 'Create Membership';
$this->params['breadcrumbs'][] = ['label' => 'Memberships', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="membership-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
