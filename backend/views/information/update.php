<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Information */

$title  = "お知らせ";
$title2 = "修正";
$this->title = sprintf("%s | %s | %s", $title2, $title, Yii::$app->name);
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $title2;
?>

<div class="information-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
