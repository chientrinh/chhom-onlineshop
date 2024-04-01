<?php

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/offer-seasonal/update.php $
 * $Id: update.php 2270 2016-03-19 08:22:27Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\OfferSeasonal
 */

use yii\helpers\Html;

$m = $model->model;
if($m instanceof \common\models\Product)
    $this->params['breadcrumbs'][] = ['label' => $m->name, 'url' => ['product/view','id'=>$m->product_id,'target'=>'offer']];
if($m instanceof \common\models\RemedyStock)
    $this->params['breadcrumbs'][] = ['label' => $m->name, 'url' => ['remedy/view','id'=>$m->remedy_id]];
?>
<div class="offer-seasonal-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <?= Html::a('削除',['delete','id'=>$model->seasonal_id],['class'=>'btn btn-danger pull-right']) ?>

</div>
