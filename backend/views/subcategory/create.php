<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Subcategory */

$this->params['breadcrumbs'][] = ['label' => '追加'];
?>
<div class="subcategory-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
