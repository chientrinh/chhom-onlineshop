<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/manufacture/update.php $
 * $Id: update.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Manufacture */

$this->title = 'Update Manufacture: ' . ' ' . $model->manufacture_id;
$this->params['breadcrumbs'][] = ['label' => 'Manufactures', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->manufacture_id, 'url' => ['view', 'id' => $model->manufacture_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="manufacture-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
