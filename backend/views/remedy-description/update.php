<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProductDescription */

$this->title = "編集";
$this->params['breadcrumbs'][] = ['label' => $model->remedy->abbr, 'url' => ['remedy/view','id'=>$model->remedy_id]];
$this->params['breadcrumbs'][] = ['label' => '補足', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title];
?>
<div class="product-description-update">

    <h1 class="pull-left"><?= Html::encode($this->title) ?></h1>

    <p class="pull-right">
    <?= Html::a("戻る",Yii::$app->request->getReferrer(), ['class' => 'btn btn-default']) ?>
    </p>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
