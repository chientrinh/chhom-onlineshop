<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/book/view.php $
 * $Id: view.php 2319 2016-03-27 07:19:25Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Book
 */

$this->params['breadcrumbs'][] = ['label' => $model->product->name, 'url' => ['/product/view', 'id' => $model->product_id]];

?>
<div class="book-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="pull-right">

        <?= Html::a("編集", ['update', 'id' => $model->product_id], ['class' => 'btn btn-primary']) ?>
        
        <?php if($prev = $model->prev): ?>
        <?= Html::a("", ['view', 'id' => $prev->product_id], [
            'title' => "前の商品",
            'class' => 'btn btn-xs btn-default glyphicon glyphicon-chevron-left'
        ]) ?>
        <?php endif ?>

        <?php if($next = $model->next): ?>
            <?= Html::a("", ['view', 'id' => $next->product_id], [
                'title' => "次の商品",
                'class' => 'btn btn-xs btn-default glyphicon glyphicon-chevron-right'
            ]) ?>
        <?php endif ?>

    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'product_id',
                'format'    => 'html',
                'value'     => Html::a($model->product_id, ['product/view','id'=>$model->product_id]),
            ],
            'author',
            'translator',
            'page',
            'pub_date',
            'publisher',
            'format.name',
            'isbn',
        ],
    ]) ?>

    <small>画像</small>
    <div class="row">
        <?php foreach($model->product->images as $image): ?>
            <div class="col-xs-6 col-md-3">
                <a class="thumbnail" href="<?=$image->url?>">
                    <?= Html::img($image->url, [
                        'alt'=> $image->basename,
                        'style'=>'max-width:100px;max-height:100px']) ?>
                </a>
                <small><?= $image->caption ?></small>
            </div>
        <?php endforeach ?>
    </div>

</div>
