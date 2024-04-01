<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/membership/index.php $
 * $Id: index.php 2785 2016-07-27 07:25:46Z mori $
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->params['breadcrumbs'][] = ['label' => '顧客', 'url' => ['/customer/index']];
$this->params['breadcrumbs'][] = ['label' => '所属'];
?>
<div class="membership-index">

    <h1>所属</h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'membership_id',
            'name',
            [
                'attribute'     => 'weight',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'attribute'=>'company_id',
                'format'   =>'html',
                'value'    => function($model) { return Html::a(strtoupper($model->company->key), ['/company/view','id'=>$model->company_id]); },
            ],
            [
                'attribute'=> 'customers',
                'label'    => '会員数',
                'format'   => 'integer',
                'value'    => function($model)
                {
                    return \common\models\CustomerMembership::find()
                                             ->select('customer_id')
                                             ->distinct()
                                             ->where(['membership_id' => $model->membership_id])
                                             ->active()
                                             ->count();
                },
                'contentOptions' => ['class' => 'text-right'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}{update}',
            ],
        ],
    ]); ?>

    <p>
        <?= Html::a('追加', ['create'], [
            'class' => 'btn btn-success',
            'data' => [
                'confirm' => "ほんとうに追加しますか",
            ],
        ]) ?>
    </p>

</div>
