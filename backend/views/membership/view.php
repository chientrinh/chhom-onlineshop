<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\CustomerMembership;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/membership/view.php $
 * $Id: view.php 2785 2016-07-27 07:25:46Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Membership
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '所属', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="membership-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class='pull-right'>
        <?= Html::a('修正', ['update', 'id' => $model->membership_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'membership_id',
            'company.name',
            'weight',
        ],
    ]) ?>

<h2>会員</h2>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => CustomerMembership::find()
                       ->where(['membership_id' => $model->membership_id])
                       ->active()
                       ->with('customer'),
            'sort'  => [ 'defaultOrder' => [ 'start_date' => SORT_DESC ] ],
        ]),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'customer_id',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->customer_id, ['/customer/view','id'=>$data->customer_id]); },
            ],
            'customer.name',
            'start_date:date',
            'expire_date:date',
        ],
    ]); ?>

</div>
