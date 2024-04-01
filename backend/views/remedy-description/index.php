<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use common\models\RemedyCategory;
use common\models\RemedyDescription;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product/index.php $
 * $Id: index.php 2067 2016-02-11 09:17:40Z mori $
 *
 * @var $this yii\web\View
 * @var $searchModel common\models\ProductSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$remedy_categories = RemedyCategory::getRemedyCategoryPulldown();
ksort($remedy_categories);

$display = ['1' => '表示のみ', '0' => '非表示のみ'];

$desc_divisions = RemedyDescription::getDivisionForView();

$this->title = '補足一覧';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>

<div class="product-description-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'layout'  => '<span class="pull-right">{summary}</span>{pager}{items}{pager}',
        'columns' => [
//             ['class' => 'yii\grid\SerialColumn'],
//             'remedy_desc_id',
            [
                'attribute' => 'remedy_id',
                'format'    => 'html',
                'value'     => function($data){
                                   if($p = $data->remedy)
                                       return Html::a($p->name, ['remedy/view','id'=>$p->remedy_id]);
                               }
            ],
            [
                'class' => 'yii\grid\DataColumn' ,
                'attribute' => 'remedy_category_id',
                'format' => 'raw',
                'filter' => Html::activeDropDownList(
                                $searchModel,
                                "remedy_category_id",
                                $remedy_categories, ['prompt' => '全表示', 'class' => 'form-control']
                            ),
                'value' => function($data){
                               if(! $data->remedy_category_id || $data->remedy_category_id == RemedyCategory::REMEDY_WHOLE) return '－';

                               return $data->remedyCategory->remedy_category_name;
                           }
            ],
            [
                'attribute' => 'title',
                'format'    => 'html',
                'value'     => function($data){
                        return Html::a($data->title, ['remedy-description/view','id'=>$data->remedy_desc_id]);
                }
            ],
            [
                'attribute' => 'body',
                'value'     => function($data) {
                                   if (! (mb_strlen($data->body) > 30))
                                       return $data->body;

                                   return mb_substr($data->body, 0 , 30). "....";
                               },
                'format'    => 'html',
            ],
            [
                'attribute' => 'seq',
                'label'     => '表示順',
                'value'     => function ($data){ return $data->seq; },
            ],
            [
                'attribute' => 'desc_division',
                'label'     => '説明区分',
                'filter'    => Html::activeDropDownList(
                                   $searchModel,
                                   "desc_division",
                                   $desc_divisions, ['prompt' => '全表示', 'class' => 'form-control']
                               ),
                'value'     => function($data){ return $data->getDivisionForView($data->desc_division); }
            ],
            [
                'attribute' => 'is_display',
                'label'     => '表示/非表示',
                'filter'    => Html::activeDropDownList(
                                   $searchModel,
                                   "is_display",
                                   $display, ['prompt' => '全表示', 'class' => 'form-control']
                               ),
                'value'     => function($data){ return $data->displayName; }
            ],
            [
                'class' => \yii\grid\ActionColumn::className(),
                'template' => '{view}{update}',
            ],
        ],
    ]); ?>

</div>
