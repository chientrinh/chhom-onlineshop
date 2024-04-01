<?php
/**
 * $URL: $
 * $Id: $
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ */

$this->title = '更新';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>
<div class="remedy-potency-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
