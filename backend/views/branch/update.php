<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/branch/update.php $
 * $Id: update.php 1113 2015-06-28 06:49:42Z mori $
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Branch */

$this->title = 'Update Branch: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '拠点', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->branch_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="branch-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
