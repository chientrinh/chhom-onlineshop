<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/storage/update.php $
 * $Id: update.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Storage */

$this->title = 'Update Storage: ' . ' ' . $model->storage_id;
$this->params['breadcrumbs'][] = ['label' => 'Storages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->storage_id, 'url' => ['view', 'id' => $model->storage_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="storage-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
