<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SearchSubcategory */
/* @var $dataProvider yii\data\ActiveDataProvider */

$companies = \common\models\Company::find()->all();
$companies = \yii\helpers\ArrayHelper::map($companies, 'company_id', 'key');

$restricts = \common\models\ProductRestriction::find()->all();
$restricts = \yii\helpers\ArrayHelper::map($restricts, 'restrict_id', 'name');

$query   = clone($dataProvider->query);
$parents = \common\models\Subcategory::find()->orWhere(['subcategory_id'=>$query->column()])
                                             ->orWhere(['parent_id'     =>$query->column()])
                                             ->all();
$parents = \yii\helpers\ArrayHelper::map($parents, 'subcategory_id', 'name');
asort($parents);

?>
<div class="subcategory-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p class="pull-right">
        <?= Html::a('マップ表示', ['map','company_id'=>Yii::$app->request->get('company_id')], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class'=>'table table-condensed'],
        'columns' => [
            [
                'attribute' => 'company_id',
                'value'     => function($data){ if($data->company) return $data->company->key; },
                'filter'    => $companies,
                'contentOptions' => ['class'=>'text-uppercase'],
            ],
            [
                'attribute' => 'parent_id',
                'format'    => 'text',
                'value'     => function($data){ if($p = $data->parent) return $p->name; },
                'filter'    => $parents,
                'headerOptions' => ['class'=>'col-md-1 col-sm-1'],
            ],
            [
                'attribute' => 'name',
                'format'    => 'html',
                'value' => function($data){ return Html::a($data->fullname, ['view','id'=>$data->subcategory_id]); },
            ],
            [
                'attribute' => 'weight',
                'headerOptions' => ['class'=>'col-md-1 col-sm-1'],
            ],
            [
                'attribute' => 'restrict_id',
                'format'    => 'html',
                'value'     => function($data){ return ($r = $data->restriction) ? $r->name : null; },
                'filter'    => $restricts,
                'headerOptions' => ['class'=>'col-md-1 col-sm-1'],
            ],
            [
                'label' => '',
                'format' => 'raw',
                'value' => function($data){
                    return Html::a('',['move-item','id'=>$data->subcategory_id],['class'=>'btn btn-xs btn-info glyphicon glyphicon-arrow-up','title'=>'一つ上へ'])
                        . ' '
                        . Html::a('',['move-item','id'=>$data->subcategory_id,'offset'=>-1],['class'=>'btn btn-xs btn-primary glyphicon glyphicon-arrow-down','title'=>'一つ下へ']);
                },
            ]
        ],
    ]); ?>

</div>
