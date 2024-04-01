<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/_recipe.php $
 * $Id: $
 *
 * $searchModel  Model
 * $dataProvider ActiveDataProvider of \backend\models\Product
 */
use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;

$searchModel->clearErrors();

$dataProvider->pagination->defaultPageSize = '40';
$dataProvider->pagination->pageSize = '40';
?>

<?= $this->render('__tabs',[
    'company' => null,
]) ?>

<?= \yii\grid\GridView::widget([
    'id' => 'product-grid-view',
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'layout'       => '{pager}{summary}{items}{pager}',
    'emptyText'    => '商品はありません',
    'columns'   => [
            [
                'label'  => '',
                'format' => 'raw',
                'value'  => function($data) use ($target)
                {
                    return $this->render('form-product', ['model'=>$data, 'target'=>$target]);
                },
                // 'headerOptions' => ['class'=>'col-md-1'],
            ],
            [
                'attribute' => 'recipe_id',
                'format'    => 'html',
                'value'     => function($data)
                {
                    return Html::a(sprintf('%06d', $data->recipe_id), ['/recipe/admin/'. $data->recipe_id]);
                },
                'headerOptions' => ['class'=>'col-md-1'],
            ],
            [
                'attribute' => 'status',
                'value'     => function($data)
                {
                    return $data->statusName;
                },
                'filter' => [
                    \common\models\Recipe::STATUS_INIT    => "発行",
                    \common\models\Recipe::STATUS_PREINIT => "仮発行",
                ],
                'headerOptions' => ['class'=>'col-md-1'],
            ],
            [
                'attribute' => 'homoeopath_name',
                'label'     => 'ホメオパス',
                'format'    => 'html',
                'value'     => function($data)
                {
                    return $data->homoeopath ? $data->homoeopath->name: null;
                },
                'headerOptions' => ['class'=>'col-md-2'],
            ],
            [
                'attribute' => 'client_name',
                'label'     => 'クライアント',
                'format'    => 'html',
                'value'     => function($data)
                {
                    return $data->client ? $data->client->name : ($data->manual_client_name ?: null);
                },
                'headerOptions' => ['class'=>'col-md-2'],
            ],
            [
                'attribute' => 'create_date',
                'format'    => ['date','php:Y-m-d D H:i'],
                'filter' => \yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute'=>'create_date',
                    'language' => 'ja',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => ['class'=>'form-control col-md-12'],
                ]),
                'headerOptions' => ['class'=>'col-md-3'],
            ],
            [
                'attribute' => 'expire_date',
                'format'    => ['date','php:Y-m-d D H:i'],
                // 'filter' => \yii\jui\DatePicker::widget([
                //     'model' => $searchModel,
                //     'attribute'=>'expire_date',
                //     'language' => 'ja',
                //     'dateFormat' => 'yyyy-MM-dd',
                //     'options' => ['class'=>'form-control col-md-12'],
                // ]),
                'headerOptions' => ['class'=>'col-md-3'],
            ],
    ],
]) ?>
