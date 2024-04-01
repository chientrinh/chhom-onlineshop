<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/staff-role/create.php $
 * $Id: create.php 1981 2016-01-14 06:04:04Z mori $
 *
 * @var $this yii\web\View
 * @var $model app\models\Staff
 */

$this->params['breadcrumbs'][] = ['label' => "従業員", 'url' => ['staff/index']];
$this->params['breadcrumbs'][] = ['label' => "役割",   'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => "追加"];
?>
<div class="staff-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
