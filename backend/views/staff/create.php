<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/staff/create.php $
 * $Id: create.php 895 2015-04-17 00:40:58Z mori $
 */

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Staff */

$this->title = "追加";
$this->params['breadcrumbs'][] = ['label' => "従業員", 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="staff-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
