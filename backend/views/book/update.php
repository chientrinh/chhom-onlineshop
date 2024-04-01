<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/book/update.php $
 * $Id: update.php 2054 2016-02-10 07:04:17Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Book
 */

$this->params['breadcrumbs'][] = ['label' => $model->product->name, 'url' => ['view', 'id' => $model->product_id]];
$this->params['breadcrumbs'][] = ['label' => "編集"];

?>
<div class="book-update">

    <h1><?= Html::encode($this->title) ?></h1>

        <?php if($model->prev): ?>
        <?= Html::a("<<", ['update', 'id' => $model->prev->product_id], ['title' => $model->prev->name]) ?>
        <?php endif ?>

        <?php if($model->next): ?>
        <?= Html::a(">>", ['update', 'id' => $model->next->product_id], ['title' => $model->next->name]) ?>
        <?php endif ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
