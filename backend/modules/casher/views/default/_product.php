<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/_product.php $
 * $Id: _product.php 2935 2016-10-08 07:23:19Z mori $
 *
 * $searchModel  Model
 * $dataProvider ActiveDataProvider of \backend\models\Product
 */
use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;

$duration = 3600;
$cache_id = 'backend/casher/search-product-category';
if(! $allModels = Yii::$app->cache->get($cache_id))
{
    $allModels = \common\models\Category::find()->all();
    Yii::$app->cache->set($cache_id, $allModels, $duration);
}

$dropDown = [];
foreach($allModels as $model)
{
    if($searchModel->company && ($searchModel->company != $model->seller->company_id))
        continue;

    $dropDown[$model->category_id] = sprintf('%s (%s)', $model->name, $model->seller->key);
}

?>

<?php if(Yii::$app->user->can('viewProduct')): ?>
<?= $this->render('__tabs',[
    'company' => $searchModel->company,
]) ?>
<?php endif ?>

<?= \yii\grid\GridView::widget([
    'id' => 'product-grid-view',
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'layout'       => '{pager}{items}{pager}{summary}',
    'columns'   => [
        [
            'attribute' => 'category_id',
            'filter'    => $dropDown,
            'value'     => function($data){ return ($c = $data->category) ? $c->name : null; },
            'headerOptions' => ['class'=>'col-md-2'],
        ],
        [
            'attribute' => 'name',
            'format'    => 'html',
            'value'     => function($data)
            {
                if($data->in_stock)
                    $options = ['title'=>'決定','class'=>'text-info'];
                else
                    $options = ['class'=> 'btn btn-danger', 'title'=>'在庫なし'];

                return Html::a($data->name, ['apply','id'=>$data->product_id,'target'=>'product'], $options);
            },
            'headerOptions' => ['class'=>'col-md-4'],
        ],
        [
            'attribute' => 'price',
            'format'    => 'currency',
            'headerOptions' => ['class'=>'col-md-1'],
            'contentOptions'=> ['class'=>'text-right'],
        ],
        [
            'attribute' => 'code',
            'format'    => 'html',
            'headerOptions'  => ['class'=>'col-md-1'],
            'contentOptions' => ['class'=>'small text-center'],
        ],
        [
            'label' => '',
            'format'    => 'html',
            'value'     => function($data){ return Html::a('詳細', ['/product/view','id'=>$data->product_id],['class'=>'btn btn-xs btn-info']); },
            'headerOptions' => ['class'=>'col-md-1'],
            'contentOptions' => ['class'=>'text-center'],
        ],
    ],
]) ?>
