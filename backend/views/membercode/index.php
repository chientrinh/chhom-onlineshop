<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/membercode/index.php $
 * $Id: index.php 3100 2016-11-23 02:49:07Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel  common\models\Membercode
 */

$this->title = '会員証NO';
$this->params['breadcrumbs'][] = ['url'=>['index'], 'label' => $this->title];

$directives = $searchModel::find()->select('directive')->distinct()->column();
$directives = array_combine($directives, $directives);
?>
<div class="membercode-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <!-- <p>
        <?= Html::a('Create Membercode', ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'code',
            'status',
            [
                'attribute' => 'customer_id',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->customer_id, ['/customer/view','id'=>$data->customer_id]); },
            ],
            'barcode',
            'pw',
            [
                'attribute' => 'directive',
                'filter'    => $directives,
            ],
            [
                'attribute' => 'migrate_id',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->migrate_id, ['/webdb/customer/view','id'=>$data->migrate_id,'db'=>$data->directive]); },
            ],
            'update_date',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}{update}',
            ],
        ],
    ]); ?>

</div>
