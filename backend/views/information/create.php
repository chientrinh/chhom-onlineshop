<?php

use yii\helpers\Html;
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/information/create.php $
 * $Id: create.php 1112 2015-06-28 06:27:04Z mori $
 */
/* @var $this yii\web\View */
/* @var $model common\models\Information */

$title  = "お知らせ";
$title2 = "追加";
$this->title = sprintf("%s | %s | %s", $title2, $title, Yii::$app->name);
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $title2;
?>

<div class="information-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
