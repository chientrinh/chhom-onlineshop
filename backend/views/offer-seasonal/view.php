<?php

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/offer-seasonal/view.php $
 * $Id: view.php 2276 2016-03-20 06:58:20Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\OfferSeasonal
 */

use yii\helpers\Html;
use yii\widgets\DetailView;


$m = $model->model;
if($m instanceof \common\models\Product)
    $this->params['breadcrumbs'][] = ['label' => $m->name, 'url' => ['product/view','id'=>$m->product_id,'target'=>'offer']];
if($m instanceof \common\models\RemedyStock)
    $this->params['breadcrumbs'][] = ['label' => $m->name, 'url' => ['remedy/view','id'=>$m->remedy_id]];

?>

<div class="offer-seasonal-view">

    <p class="pull-right">
        <?= Html::a('編集', ['update', 'id' => $model->seasonal_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <h1><?= $this->context->title ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'master.name',
            ],
            'ean13',
            'grade_id',
            'branch_id',
            'discount_rate',
            'point_rate',
            'start_date',
            'end_date',
        ],
    ]) ?>

</div>
