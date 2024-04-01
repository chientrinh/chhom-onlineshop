<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/customer/create.php $
 * $Id: create.php 3851 2018-04-24 09:07:27Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Customer
 */

use yii\helpers\Html;

$title = "顧客を作成";

$this->params['breadcrumbs'][] = ['label' => $title];

$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels);
$this->title = implode(' | ', $labels) . ' | ' . Yii::$app->name;

?>

<div class="customer-create">

    <h1><?= Html::encode($title) ?></h1>

    <?= $this->render('_form', [
        'model'  => $model,
        'parent' => $parent,
        'mode'   => $mode
    ]) ?>

</div>
