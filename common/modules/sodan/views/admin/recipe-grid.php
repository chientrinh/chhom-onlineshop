<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/admin/recipe-grid.php $
 * $Id: recipe-grid.php 3851 2018-04-24 09:07:27Z mori $
 *
 * $this \yii\web\View
 * $dataProvider
 * $searchModel
 */
use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;

?>

<?= \yii\grid\GridView::widget([
    'tableOptions' => ['class'=>'table table-condensed table-striped'],
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'layout'       => '{pager}{items}{summary}',
    'showOnEmpty'  => false,
    'emptyText'    => 'まだありません',
    'columns'      => [
        [
            'attribute' => 'recipe_id',
            'format'    => 'html',
            'value'     => function($data){ return Html::a(sprintf('%06d',$data->recipe_id),['/recipe/admin/view','id'=>$data->recipe_id]); },
        ],
        [
            'attribute' => 'create_date',
            'format'    => ['date','php:Y-m-d H:i']
        ],
        [
            'attribute' => 'homoeopath_id',
            'format'    => 'html',
            'value'     => function($data)
            {
                if($data->homoeopath)
                    return Html::tag('span',$data->homoeopath->homoeopathname);
            },
        ],
        [
            'attribute' => 'client_id',
            'format'    => 'html',
            'value'     => function($data)
            {
                if($data->client)
                    return Html::tag('span',$data->client->name);
            }
        ],
        [
            'attribute' => 'items',
            'value'     => function($data){ return $data->itemCount; },
        ],
        [
            'attribute' => 'status',
            'value'     => function($data){ return $data->statusName; },
        ],
    ],
]) ?>
