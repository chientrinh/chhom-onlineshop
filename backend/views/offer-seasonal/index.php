<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/offer-seasonal/index.php $
 * $Id: index.php 3852 2018-04-26 04:54:39Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = sprintf('%s | %s', $this->context->title, Yii::$app->name);
$this->params['breadcrumbs'][] = $this->context->title;
?>
<div class="offer-seasonal-index">

    <h1>
        <?= Html::encode($this->context->title) ?>
        <small>(期間／会員／拠点 別)</small>
    </h1>

    <p class="pull-right">
        <?= Html::a('追加', ['create'], ['class' => 'btn btn-success', 'style' => 'margin-right:10px;']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class'=>'table table-condensed table-striped table-bordered'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'ean13',
            [
                'label'     => '品名',
                'format'    => 'html',
                'value'     => function($data){
                    $model = $data->model;
                    if($model instanceof \common\models\Product)
                        return Html::a($model->name, ['/product/view','id'=>$model->product_id,'target'=>'offer']);

                    if($model instanceof \common\models\RemedyStock)
                        return Html::a($model->name, ['/remedy/view','id'=>$model->remedy_id]);

                    if($master = $data->master) return $master->name;
                },
            ],
            [
                'attribute' => 'grade_id',
                'value'     => function($data){ if($g = $data->grade) return $g->longname; }
            ],
            [
                'attribute' => 'branch_id',
                'value'     => function($data){ if($b = $data->branch) return $b->name; }
            ],
            [
                'attribute' => 'discount_rate',
                'contentOptions' => ['class' => 'text-right'],
            ],
            [
                'attribute' => 'point_rate',
                'contentOptions' => ['class' => 'text-right'],
            ],
            'start_date',
            'end_date',
            [
                'label'     => '編集',
                'format'    => 'raw',
                'value'     => function($data){
                    return Html::a('編集', ['update','id' => $data->seasonal_id], ['class' => 'btn btn-default']);
                },
            ],
        ],
    ]); ?>
</div>
