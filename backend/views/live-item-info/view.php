<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use \common\models\LiveInfo;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/live-item-info/view.php $
 * $Id: view.php 2992 2016-10-19 07:37:38Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Streaming
 */

$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view','id'=>$model->id]];
?>
<div class="live-item-info-view">

    <p class="pull-right">
        <?= Html::a('編集', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= !$model->isNewRecord ? \yii\helpers\Html::a("削除", ['live-item-info/delete','id'=>$model->id],['class'=>'btn btn-danger', 'title'=>'確認', 'data' =>['confirm'=>"このリンクを削除します。よろしいですか？"]]) : null ?>
        <?= Html::a('',['view','id'=>$model->id -1],['class'=>'btn btn-sm btn-default glyphicon glyphicon-chevron-left'])?>
        <?= Html::a('',['view','id'=>$model->id +1],['class'=>'btn btn-sm btn-default glyphicon glyphicon-chevron-right'])?>
    </p>

    <h1><?= Html::encode($model->name) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'info_id',
                'format'    => 'raw',
                'value'     => $model->info ? Html::a($model->info->name, ['live-info/view', 'id'=>$model->info_id]) : "",
            ],
            [
                'attribute' => 'product_id',
                'format'    => 'raw',
                'value'     => $model->product ? Html::a($model->product->name, ['product/view', 'id'=>$model->product_id]) : "",
            ],

        ],
    ]) ?>

</div>
