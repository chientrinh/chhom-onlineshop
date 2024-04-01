<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product-grade/update.php $
 * $Id: update.php 2286 2020-04-28 12:11:00Z kawai $
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProductGrade */

$this->params['breadcrumbs'][] = ['label' => '会員ランク別商品価格 '.$model->product_grade_id, 'url' => ['view', 'id' => $model->product_grade_id]];
$this->params['breadcrumbs'][] = '編集';
?>
<div class="product-grade-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
