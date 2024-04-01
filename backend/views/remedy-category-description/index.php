<?php
/**
 * $URL:  $
 * $Id: $
 */

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\RemedyCategory;
use common\models\RemedyCategoryDescription;


$display = ['1' => '表示のみ', '0' => '非表示のみ'];
$desc_divisions = RemedyCategoryDescription::getDivisionForView();

/* @var $this yii\web\View */
/* @var $searchModel common\models\SearchRemedyCategryDescription */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="remedy-potency-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('説明追加', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => [
            [
                'class' => 'yii\grid\SerialColumn',
            ],
            [
                'class'     => 'yii\grid\DataColumn' ,
                'attribute' => 'remedy_category_id',
                'format'    => 'raw',
                'filter'    => Html::activeDropDownList(
                                   $searchModel,
                                   "remedy_category_id",
                                   RemedyCategory::getRemedyCategoryPulldown(), ['prompt' => '全表示', 'class' => 'form-control']
                               ),
                'value'     => function($data){
                                   return $data->remedyCategory->remedy_category_name;
                               },
            ],
            [
                'attribute' => 'title',
                'value'     => function ($data){
                                   return $data->title;
                },
                'label'     => '見出し',
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
                'class'     => 'yii\grid\ActionColumn',
                'template'  => '{view}{update}',
            ],
        ],
    ]); ?>

</div>
