<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy-stock/index.php $
 * $Id: index.php 3196 2017-02-26 05:11:03Z naito $
 *
 * @var $this yii\web\View
 * @var $searchModel common\models\SearchRemedyStock
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->params['breadcrumbs'][] = ['label' => '既製レメディー', 'url' => ['index']        ];

$dataProvider->setSort([
    'attributes' => [
        'remedy' => [
            'asc'  => ['remedy.abbr'=>SORT_ASC ],
            'desc' => ['remedy.abbr'=>SORT_DESC],
        ],
        'on_sale',
        'potency_id',
        'prange_id',
        'vial_id',
        'restrict_id',
        'in_stock',
    ],
]);
?>
<div class="remedy-stock-index">

    <?= Html::a('CSV', Url::current(['format'=>'csv']), ['class'=>'btn btn-default pull-right']) ?>
    <h1>既製レメディー</h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'filterSelector' => 'select[name="per-page"]',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'barcode',
            ],
            'name',
            [
                'label'     => $searchModel->getAttributeLabel('pickcode'),
                'attribute' => 'pickcode',
            ],
            [
                'attribute' => 'remedy',
                'format'    => 'html',
                'value'     => 'remedy.abbr',
                'value'     => function($data){ return Html::a($data->remedy->abbr, ['/remedy/view','id'=>$data->remedy_id]); },
            ],
            [
                'attribute' => 'potency_id',
                'label'     => "ポーテンシー",
                'filter'    => \yii\helpers\ArrayHelper::map(\common\models\RemedyPotency::find()->all(), 'potency_id', 'name'),
                'value'     => function($data){ return $data->potency->name; },
            ],
            [
                'attribute' => 'prange_id',
                'label'     => "価格帯",
                'filter'    => \yii\helpers\ArrayHelper::map(\common\models\RemedyPriceRange::find()->all(), 'prange_id', 'name'),
                'value'     => function($data){ return $data->prange->name; },
            ],
            [
                'attribute' => 'vial_id',
                'label'     => "容器",
                'filter'    => \yii\helpers\ArrayHelper::map(\common\models\RemedyVial::find()->all(), 'vial_id', 'name'),
                'value'     => function($data){ return $data->vial->name; },
            ],
            [
                'attribute' => 'on_sale',
                'label'     => $searchModel->getAttributeLabel('remedy.on_sale'),
                'filter'    => [1 => "OK", 0 => "NG"],
                'value'     => function($data){ return $data->remedy->on_sale ? "OK" : "NG"; },
            ],
            [
                'attribute' => 'restrict_id',
                'filter'    => \yii\helpers\ArrayHelper::map(\common\models\ProductRestriction::find()->all(), 'restrict_id', 'name'),
                'value'     => function($data){ return $data->restriction->name; },
            ],
            [
                'attribute' => 'price',
                'format'    => 'html',
                'value'     => function($data){ return '&yen;' . number_format($data->price); },
                'contentOptions' => ['class' => 'number'],
            ],
            [
                'attribute' => 'in_stock',
                'filter'    => [1 => "OK", 0 => "NG"],
                'value'     => function($data){ return $data->in_stock ? "OK" : "NG"; },
            ],
            [
                'class' => \yii\grid\ActionColumn::className(),
                'template' => '{view}{update}',
            ],
        ],
    ]); ?>

</div>
