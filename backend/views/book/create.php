<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/book/create.php $
 * $Id: create.php 2054 2016-02-10 07:04:17Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Book
 */


$this->params['breadcrumbs'][] = ['label' => '追加'];
?>
<div class="book-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
