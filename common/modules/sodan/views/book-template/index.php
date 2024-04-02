<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/wait-list/index.php $
 * $Id: index.php 1876 2015-12-15 15:53:35Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

?>
<div class="wait-list-index">

    <p class="pull-right">
        <?= Html::a('追加', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <h1>予約票テンプレート</h1>
    <p>予約票に記載する文言を管理します。</p>
    <?= \yii\grid\GridView::widget([
    'tableOptions' => ['class'=>'table table-condensed table-striped'],
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'showOnEmpty'  => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'template_id',
                'format'    => 'html',
                'value'     => function($data){
                    return Html::a(sprintf('%06d', $data->template_id), ['view','id' => $data->template_id]);
                },
                'filter'    => false
            ],
            [
                'attribute' => 'body',
                'format'    => 'html',
                'value'     => function($data){ return nl2br($data->body); },
            ],
            [
                'attribute' => 'create_date',
                'format'    => 'html',
                'value'     => function($data){ return $data->create_date; },
                'filter'    => false
            ],
            [
                'attribute' => 'update_date',
                'format'    => 'html',
                'value'     => function($data){ return $data->update_date; },
                'filter'    => false
            ],
        ],
    ]); ?>
</div>
