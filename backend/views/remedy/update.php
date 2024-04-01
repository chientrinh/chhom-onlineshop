<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy/update.php $
 * $Id: update.php 969 2015-04-30 02:53:47Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Remedy */

$this->title = "";
$this->params['breadcrumbs'][] = ['label' => "レメディー", 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->remedy_id]];
$this->params['breadcrumbs'][] = "編集";
?>
<div class="remedy-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
