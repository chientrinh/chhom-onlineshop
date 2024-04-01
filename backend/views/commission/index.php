<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/commission/index.php $
 * $Id: index.php 2797 2016-07-31 01:53:11Z mori $
 */

$this->params['breadcrumbs'][] = ['label' => '一覧'];

$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels);
$labels[] = Yii::$app->name;
$this->title = implode(' | ', $labels);

?>
<div class="commission-index">

    <h1>手数料</h1>
    <p class="help-block">
        当社から代理店や本部ホメオパスへ支払う手数料を表示します
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'commision_id',
                'format'    => 'html',
                'value'     => function($data){ return Html::a(sprintf('%06d',$data->commision_id),['view','id'=>$data->commision_id]); }
            ],
            [
                'attribute' => 'create_date',
                'format'    => ['date','php:Y-m-d H:i'],
            ],
            [
                'attribute' => 'company_id',
                'value'     => function($data){ return $data->company ? $data->company->name : null; }
            ],
            [
                'attribute' => 'customer_id',
                'value'     => function($data){ return $data->customer ? $data->customer->name : null; }
            ],
            'purchase.note',
            [
                'attribute' => 'fee',
                'format'    => 'currency',
                'contentOptions' => ['class'=>'text-right'],
            ],
        ],
    ]); ?>

    <p>
        <?= Html::a('追加', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

</div>
