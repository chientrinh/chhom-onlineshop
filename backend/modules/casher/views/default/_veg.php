<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/_veg.php $
 * $Id: _veg.php 3509 2017-07-26 04:50:29Z kawai $
 *
 * $searchModel  Model
 * $dataProvider ActiveDataProvider of \backend\models\Product
 */
use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;

$searchModel->clearErrors();
?>

<?= $this->render('__tabs',[
    'company' => null,
]) ?>

<?= \yii\grid\GridView::widget([
    'id' => 'product-grid-view',
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'layout'       => '{pager}{items}{pager}{summary}',
    'columns'   => [
        [
            'label'  => '',
            'format' => 'raw',
            'value'  => function($data) use ($target)
            {
                return $this->render('_veg_form',['model'=>$data, 'target'=>$target]);
            },
            // 'headerOptions' => ['class'=>'col-apply'],
            'contentOptions' => ['class' => 'col-md-3'],
        ],
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['class'=>'col-searial'],
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
            'value'     => function($data){ return $data->name; },
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
            'headerOptions' =>['class'=>'col-md-4   '],
        ],

    ],
]) ?>
