<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/agency-rating/view.php $
 * $Id: view.php 1802 2015-11-13 16:11:19Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\AgencyRating
 */

?>
<div class="agency-rating-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->rating_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'customer_id',
                'format'    => 'html',
                'value'     => Html::a($model->customer->name, ['/customer/view','id'=>$model->customer_id]),
            ],
            [
                'attribute' => 'company_id',
                'value'     => $model->company->key,
            ],
            'discount_rate:integer',
            [
                'attribute' => 'start_date',
                'format'    => ['date','php:Y-m-d'],
            ],
            [
                'attribute' => 'end_date',
                'format'    => ['date','php:Y-m-d'],
            ],
        ],
    ]) ?>

</div>
