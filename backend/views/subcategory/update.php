<?php

use yii\helpers\Html;

/*
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/subcategory/update.php $
 * $Id: update.php 2019 2016-01-28 08:09:29Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Subcategory
 */

$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->subcategory_id]];
$this->params['breadcrumbs'][] = ['label' => '編集'];
?>
<div class="subcategory-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
