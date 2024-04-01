<?php

use yii\helpers\Html;
use yii\grid\GridView;
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/information/index.php $
 * $Id: index.php 1112 2015-06-28 06:27:04Z mori $
 */
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$title = "お知らせ";
$this->title = sprintf("%s | %s", $title, Yii::$app->name);
$this->params['breadcrumbs'][] = ['label'=>$title, 'url' => ['/information']];
?>
<div class="information-index">

    <h1><?= Html::encode($title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'pub_date:date',
            'content',
            'url:url',
            [
                'attribute' => 'company_id',
                'value'     => function($model){ return strtoupper($model->company->key); },
            ],
            [
                'attribute' => 'updated_by',
                'value'     => function($model){ return $model->updator->name; },
            ],
            [
                'label' => "状態",
                'value' =>  function($model){ return $model->isExpired() ? "失効" : "有効"; }
            ],
            'update_date:date',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <p>
    <?= Html::a('追加', ['create'], ['class' => 'btn btn-success']) ?>
    <?= Html::a('失効したお知らせを見る', ['index', 'expired'=> true], ['class' => 'btn btn-default']) ?>
    </p>

</div>
