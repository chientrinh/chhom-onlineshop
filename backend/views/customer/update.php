<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/customer/update.php $
 * $Id: update.php 1867 2015-12-11 03:15:44Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Customer
 */

use yii\helpers\Html;

$title = "顧客を更新";

$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->customer_id]];
$this->params['breadcrumbs'][] = ['label' => "更新"];

$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels);
$this->title = implode(' | ', $labels) . ' | ' . Yii::$app->name;

?>

<div class="customer-update">

    <h1><?= Html::encode($title) ?></h1>

    <?= $this->render('_form', [
        'model'    => $model,
    ]) ?>

</div>
