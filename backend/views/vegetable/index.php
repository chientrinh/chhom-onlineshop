<?php

use yii\helpers\Html;
use yii\grid\GridView;
use \common\models\Vegetable;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/vegetable/index.php $
 * $Id: index.php 3511 2017-07-26 06:22:46Z kawai $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->params['breadcrumbs'][] = ['label' => '一覧', 'url' => ['index'] ];
?>
<div class="vegetable-index">
    <div class="col-md-12">
        <h1>野菜</h1>
        <p class="pull-right">
            <?= Html::a('追加', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

    
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'layout'       => '{pager}{summary}{items}{pager}',
            'columns' => [
                [
                    'attribute' => 'veg_id',
                    'value'     => function($data){ return $data->veg_id; },
                    // 'headerOptions' =>['class'=>'js-zenkaku-to-hankaku'], //col-md-1'],
                ],
                [
                    'attribute' => 'division',
                    'label'     => '種別',
                    'value'     => function($data){ return Vegetable::getDivision($data->division); },
                    'filter'    => Vegetable::getDivision(),
                    'headerOptions' =>['class'=>'col-md-1'],
                ],
                [
                    'attribute' => 'origin_area',
                    'label'     => '原産地',
                    'format'    => 'raw',
                    'value'     => function($data){ return $data->origin_area; },
                    'headerOptions' =>['class'=>'col-md-1'],
                ],
                [
                    'attribute' => 'name',
                    'label'     => '品名',
                    'format'    => 'html',
                    'value'     => function($data){ return Html::a($data->name, ['view','id'=>$data->veg_id]); },
                    'headerOptions' =>['class'=>'col-md-3'],
                ],
                [
                    'attribute' => 'kana',
                    'label'     => 'かな',
                    'value'     => function($data){ return $data->kana; },
                    'headerOptions' =>['class'=>'col-md-3'],
                ],
                [
                    'attribute' => 'capacity',
                    'label'     => '容量',
                    'value'     => function($data){ return $data->capacity ? $data->capacity. "g" : null; },
                    'headerOptions' =>['class'=>'col-md-1'],
                ],
                [
                    'attribute' => 'print_name',
                    'label'     => '印刷用名称',
                    'value'     => function($data){ return $data->print_name; },
                    'headerOptions' =>['class'=>'col-md-3'],
                ],
            ],
        ]); ?>
    </div>
</div>
