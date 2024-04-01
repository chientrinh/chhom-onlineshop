<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/category/update.php $
 * $Id: update.php 2286 2016-03-21 06:11:00Z mori $
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Category */

$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->category_id]];
$this->params['breadcrumbs'][] = '編集';
?>
<div class="category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
