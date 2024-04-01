<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy/view.php $
 * $Id: view.php 3895 2018-05-24 07:52:52Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Remedy
 */

$this->title = $model->abbr;
$this->params['breadcrumbs'][] = ['label' => "レメディー", 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="remedy-view">
<div class="row">

  <div class="col-md-6">
    <h1><?= Html::encode($this->title) ?></h1>
  </div>

  <div class="pull-right">

    <?= Html::a("編集", ['update', 'id' => $model->remedy_id], ['class' => 'btn btn-primary']) ?>

     <?php if($model->prev): ?>
        <?= Html::a( Html::tag('i','',['class'=>'glyphicon glyphicon-chevron-left']), ['viewbyname','name'=>$model->prev->name], ['class'=>'btn btn-xs btn-default']) ?>
    <?php endif ?>

    <?php if($model->next): ?>
        <?= Html::a( Html::tag('i','',['class'=>'glyphicon glyphicon-chevron-right']), ['viewbyname','name'=>$model->next->name], ['class'=>'btn btn-xs btn-default','title'=>'次']) ?>
    <?php endif ?>

  </div>

</div>

<?= (!$model->on_sale) ? '<div class="col-md-12 alert alert-danger" role="alerts">この商品は一般には販売しません</div>' : '' ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'remedy_id',
            'abbr',
            'latin',
            'ja',
            'advertise:html',
            'concept',
            [
                'attribute' => 'on_sale',
                'value'     => $model->on_sale ? 'OK' : 'NG',
            ],
            [
                'attribute' => 'restrict_id',
                'value'     => $model->restriction->name,
            ],
        ],
    ]) ?>

    <?= $this->render('_stock', ['caption' => "単品レメディー", 'allModels' => $model->getProducts()]) ?>

    <?= $this->render('_stock', ['caption' => "滴下のための素材", 'allModels' => $model->getDroppables(false,false)->all()]) ?>

    <?= $this->render('_description', ['caption' => "商品補足説明", 'allModels' => $model->getRemedyDescriptionForBack()->all(), 'remedy_id' => $model->remedy_id]) ?>

<small>画像</small>
<div class="row">
<?php foreach($model->images as $image): ?>
  <div class="col-xs-6 col-md-3">
    <a class="thumbnail" href="<?=$image->url?>">
        <?= Html::img($image->url, ['alt'=> $image->basename, 'style'=>'max-width:100px;max-height:100px']) ?>
    </a>
    <small><?= $image->caption ?></small>
  </div>
<?php endforeach ?>
</div>


<?= \yii\grid\GridView::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => \common\models\ProductMaster::find()->where(['remedy_id' => $model->remedy_id]),
        'sort'  => ['defaultOrder' => ['name' => SORT_ASC]],
        'pagination' => false,
    ]),
    'caption' => '表示名',
    'layout'  => '{items}',
    'columns' => [
        'name',
        [
            'attribute' => 'potency_id',
            'value'     => function($data){ return ($p = $data->potency) ? $p->name : null; },
            'headerOptions' => ['class' => 'col-md-1'],
        ],
        [
            'attribute' => 'vial_id',
            'value'     => function($data){ return ($v = $data->vial) ? $v->name : null; },
            'headerOptions' => ['class' => 'col-md-1'],
        ],
        [
            'attribute' => 'restrict_id',
            'value'     => function($data){ return ($data) ? $data->restriction->name : null; },
            'headerOptions' => ['class' => 'col-md-1'],
        ],
        [
            'label' => '',
            'format'=> 'html',
            'value' => function($data){ return Html::a('', ['/product-master/update','id'=>$data->ean13],['class'=>'glyphicon glyphicon-pencil']); },
            'headerOptions' => ['class' => 'col-md-1'],
        ],
    ],
]) ?>
</div>
