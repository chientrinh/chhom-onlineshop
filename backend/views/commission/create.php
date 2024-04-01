<?php

use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $model common\models\Commission
 * 
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/commission/create.php $
 * $Id: create.php 1878 2015-12-16 03:08:49Z mori $
 */

$title = '追加';
$this->params['breadcrumbs'][] = ['label' => $title,'url'=>['view','id'=>$model->commision_id]];
$this->params['breadcrumbs'][] = ['label' => '更新'];

$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels);
$labels[] = Yii::$app->name;
$this->title = implode(' | ', $labels);
?>
<div class="commission-create">

    <h1><?= Html::encode($title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
