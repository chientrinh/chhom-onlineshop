<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/live-info/update.php $
 * $Id: update.php 2286 2020-04-28 12:11:00Z kawai $
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Streaming */

$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->info_id]];
$this->params['breadcrumbs'][] = '編集';
?>
<div class="live-info-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
