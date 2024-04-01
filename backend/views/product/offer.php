<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product/offer.php $
 * $Id: offer.php 2272 2016-03-20 02:30:22Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Product
 */

if($company = $model->seller)
    $this->params['breadcrumbs'][] = [
        'label' => $company->name,
        'url' => ['index','company'=>$company->company_id]
    ];

$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view','id'=>$model->product_id]];
$this->params['breadcrumbs'][] = ['label' => 'ご優待'];

$jscode = "
$('#trigger-btn').click(function(){
    $(this).hide();
    $('#create-form').show();
    return false;
});1
";
$this->registerJs($jscode);
?>

<div class="product-view-sales">

    <p class="pull-right">
        <?php if($model->prev): ?>
        <?= Html::a("", ['view', 'target' => 'offer', 'id' => $model->prev->product_id], ['title' => "前の商品",'class'=>'btn btn-xs btn-default glyphicon glyphicon-chevron-left']) ?>
        <?php endif ?>

        <?php if($model->next): ?>
        <?= Html::a("", ['view', 'target' => 'offer', 'id' => $model->next->product_id], ['title' => "次の商品",'class'=>'btn btn-xs btn-default glyphicon glyphicon-chevron-right']) ?>
        <?php endif ?>
    </p>

    <h1><?= Html::encode($model->name) ?></h1>

    <?= $this->render('_tab',['model'=>$model]) ?>

    <?= GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query'      => $model->getOffer()->with('grade'),
            'pagination' => false,
            'sort'       => [
                'attributes' => [
                    'grade_id',
                    'discount_rate',
                    'point_rate',
                ],
                'defaultOrder' => ['grade_id'=>SORT_DESC],
            ],
        ]),
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'layout'       => '{items}',
        'caption'      => 'ご優待 初期値',
        'columns'      => [
            [
                'attribute' => 'grade_id',
                'format'    => 'html',
                'value'     => function($data)
                {
                    if($data->grade)
                        return Html::a($data->grade->longname, ['/customer-grade/view','id'=>$data->grade_id]);
                    else
                        return "全員";
                }
            ],
            'discount_rate:integer',
            'point_rate:integer',
        ],
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query'      => $model->getSeasonalOffer()->with(['grade','branch'])->current(),
            'pagination' => false,
            'sort'       => [
                'attributes' => [
                    'grade_id',
                    'branch_id',
                    'discount_rate',
                    'point_rate',
                    'start_date',
                    'end_date',
                ],
            ],
        ]),
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'rowOptions'   => ['class' => 'info'],
        'layout'       => '{items}',
        'showOnEmpty'  => false,
        'emptyText'    => '現在有効なご優待はありません',
        'caption'      => 'ご優待 期間／拠点',
        'columns'      => [
            [
                'attribute' => 'branch_id',
                'format'    => 'html',
                'value'     => function($data){
                    if($data->branch)
                        return Html::a($data->branch->name, ['/branch/view','id'=>$data->branch_id]);
                    else
                        return "全拠点";
                }
            ],
            [
                'attribute' => 'grade_id',
                'format'    => 'html',
                'value'     => function($data){
                    if($data->grade)
                        return Html::a($data->grade->longname, ['/customer-grade/view','id'=>$data->grade_id]);
                    else
                        return "全員";
                }
            ],
            'discount_rate:integer',
            'point_rate:integer',
            [
                'attribute' => 'start_date',
            ],
            [
                'attribute' => 'end_date',
            ],
            [
                'class' => \yii\grid\ActionColumn::className(),
                'template'=> '{update}',
                'urlCreator'=> function( $action, $model, $key, $index ){
                    return ['/offer-seasonal/update','id'=>$model->seasonal_id];
                },
            ],
        ],
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query'  => $model->getSeasonalOffer()->with(['grade','branch'])->past(),
            'pagination' => false,
            'sort'       => [
                'attributes' => [
                    'grade_id',
                    'branch_id',
                    'discount_rate',
                    'point_rate',
                    'start_date',
                    'end_date',
                ],
                'defaultOrder' => ['grade_id'=>SORT_DESC],
            ],
        ]),
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'layout'       => '{items}',
        'showOnEmpty'  => false,
        'emptyText'    => '過去のご優待はありません',
        'caption'      => '過去のご優待',
        'columns'      => [
            [
                'attribute' => 'branch_id',
                'format'    => 'html',
                'value'     => function($data){
                    if($data->branch)
                        return Html::a($data->branch->name, ['/branch/view','id'=>$data->grade_id]);
                    else
                        return "全拠点";
                }
            ],
            [
                'attribute' => 'grade_id',
                'format'    => 'html',
                'value'     => function($data){
                    if($data->grade)
                        return Html::a($data->grade->longname, ['/customer-grade/view','id'=>$data->grade_id]);
                    else
                        return "全員";
                }
            ],
            'discount_rate:integer',
            'point_rate:integer',
            [
                'attribute' => 'start_date',
            ],
            [
                'attribute' => 'end_date',
            ],
        ],
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query'      => $model->getSeasonalOffer()->with(['grade','branch'])->future(),
            'pagination' => false,
            'sort'       => [
                'attributes' => [
                    'grade_id',
                    'branch_id',
                    'discount_rate',
                    'point_rate',
                    'start_date',
                    'end_date',
                ],
                'defaultOrder' => ['grade_id'=>SORT_DESC],
            ],
        ]),
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'layout'       => '{items}',
        'showOnEmpty'  => false,
        'emptyText'    => '将来のご優待は予定されていません',
        'caption'      => '将来のご優待',
        'columns'      => [
            [
                'attribute' => 'branch_id',
                'format'    => 'html',
                'value'     => function($data){
                    if($data->branch)
                        return Html::a($data->branch->name, ['/branch/view','id'=>$data->grade_id]);
                    else
                        return "全拠点";
                }
            ],
            [
                'attribute' => 'grade_id',
                'format'    => 'html',
                'value'     => function($data){
                    if($data->grade)
                        return Html::a($data->grade->longname, ['/customer-grade/view','id'=>$data->grade_id]);
                    else
                        return "全員";
                }
            ],
            'discount_rate:integer',
            'point_rate:integer',
            [
                'attribute' => 'start_date',
            ],
            [
                'attribute' => 'end_date',
            ],
            [
                'class' => \yii\grid\ActionColumn::className(),
                'template'=> '{update}',
                'urlCreator'=> function( $action, $model, $key, $index ){
                    return ['/offer-seasonal/update','id'=>$model->seasonal_id];
                },
            ],
        ],
    ]) ?>

    <?= Html::a("追加",'#',['id'=>'trigger-btn','class'=>'btn btn-success']) ?>

    <div id="create-form" class="well" style="display:none">
    <?php 
        $newModel = new \common\models\OfferSeasonal([
            'ean13' => $model->barcode,
            'grade_id'  => null,
            'branch_id' => -1,
        ]);
    ?>
    <?= $this->render('/offer-seasonal/_form',['model'=> $newModel]) ?>
    </div>

</div>
