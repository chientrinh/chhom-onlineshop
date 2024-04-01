<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Subcategory */

foreach($model->ancestors as $a)
    $this->params['breadcrumbs'][] = ['label' => $a->name, 'url' => ['view','id'=>$a->subcategory_id]];

$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view','id'=>$model->subcategory_id]];

$csscode = '
.detail-view th {
  width: 20%;
}
div.title input {
display:none;
}
';
$this->registerCss($csscode);

?>
<div class="subcategory-view">

    <p class="pull-right">
        <?= Html::a('編集', ['update', 'id' => $model->subcategory_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a( Html::tag('i','',['class'=>'glyphicon glyphicon-chevron-left']), ['view','id'=>$model->subcategory_id -1], ['class'=>'btn btn-xs btn-default']) ?>
        <?= Html::a( Html::tag('i','',['class'=>'glyphicon glyphicon-chevron-right']), ['view','id'=>$model->subcategory_id +1], ['class'=>'btn btn-xs btn-default']) ?>
    </p>

    <h1><?= Html::encode($model->fullname) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'company.name',
            [
                'attribute' => 'restrict_id',
                'format'    => 'html',
                'value'     => $model->restriction->name . Html::tag('p', $model->getAttributeHint('restrict_id'),['class'=>'help-block']),
            ],
            [
                'attribute' => 'children',
                'format'    => 'html',
                'value'     => \yii\grid\GridView::widget([
                    'dataProvider'=> new \yii\data\ActiveDataProvider(['query'       => $model->getChildren(),
                                                                       'pagination'  => false,
                    ]),
                    'columns' => [
                        [
                            'attribute' => 'name',
                            'format'    => 'html',
                            'value'     => function($data){ return Html::a($data->name,['view','id'=>$data->subcategory_id]); },
                        ],
                        [
                            'attribute' => 'restrict_id',
                            'value'     => function($data){ if($r = $data->restriction) return $r->name; },
                        ],
                        'weight',
                        [
                            'label'     => '',
                            'format'    => 'raw',
                            'value'     => function($data)
                            {
                                return Html::a('',['move-item','id'=>$data->subcategory_id],['class'=>'btn btn-xs btn-info glyphicon glyphicon-arrow-up','title'=>'一つ上へ'])
                                    . ' '
                                    . Html::a('',['move-item','id'=>$data->subcategory_id,'offset'=>-1],['class'=>'btn btn-xs btn-primary glyphicon glyphicon-arrow-down','title'=>'一つ下へ']);

                            },
                        ],
                    ],
                    'layout' => '{items}',
                    'showHeader' => true,
                    'tableOptions' => ['class'=>'table table-condensed'],
                ]),
//Html::ul($model->children,['item'=>function($item,$index){ return Html::tag('li',Html::a($item->name,['view','id'=>$item->subcategory_id])); }]),
            ],
        ],
    ]) ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getProducts()->orderBy('kana'),
        ]),
        'caption' => '商品',
        'columns' => [
            'ean13',
            'name',    
            [
                'attribute' => 'kana',
                'format' => 'html',
                'value' => function($data)
                {
                    if($data->product_id)
                        return Html::a($data->kana, ['product/view','id'=>$data->product_id]);

                    return Html::a($data->kana,['remedy-stock/view',
                                                'remedy_id'  => $data->remedy_id,
                                                'potency_id' => $data->potency_id,
                                                'vial_id'    => $data->vial_id]);
                },
            ],
            [
                'attribute' => 'price',
                'format'    => 'currency',
                'contentOptions' => ['class' => 'text-right'],
            ],
            [
                'attribute' => 'restrict_id',
                'value'     => function($data){ return $data->restriction->name; },
            ],
        ],
    ]) ?>

    <?= $this->render('_product',['model'=>$model]) ?>

</div>

