<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use common\models\Category;
use common\models\CustomerGrade;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/offer/index.php $
 * $Id: index.php 3290 2017-05-14 09:22:48Z naito $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->params['breadcrumbs'][] = ['label' => $this->context->title, 'url' => 'index'];

$categories = ArrayHelper::map(
    Category::find()->with('seller')->all(),
    'category_id',
    function($elem){ return strtoupper($elem->seller->key) .':'. $elem->name; }
);
asort($categories);

$grades = ArrayHelper::map(CustomerGrade::find()->all(), 'grade_id', 'longname');

?>
<div class="offer-index">

    <h1>
        <?= $this->context->title ?>
    </h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'tableOptions' => ['class'=>'table table-condensed table-striped table-bordered'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'company_id',
                'value'     => function($data){ return $data->category->seller->name; },
            ],
            [
                'attribute' => 'category_id',
                'value'     => function($data){ return $data->category->name; },
                'filter'    => $categories,
            ],
            [
                'attribute' => 'grade_id',
                'value'     => function($data){ return $data->grade->longname; },
                'filter'    => $grades,
            ],
            [
                'attribute' => 'discount_rate',
                'contentOptions' => ['class'=>'text-right'],
            ],
            [
                'attribute' => 'point_rate',
                'contentOptions' => ['class'=>'text-right'],
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
            ],
        ],
    ]); ?>

    <p>
    　　<?php if (Yii::$app->user->identity->hasRole(['wizard'])) : ?>
            <?= Html::a('追加', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif ?>
    </p>

</div>
