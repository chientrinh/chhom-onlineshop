<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy-stock/create.php $
 * $Id: create.php 3196 2017-02-26 05:11:03Z naito $
 *
 * @var $this yii\web\View
 * @var $model common\models\RemedyStock */

$this->params['breadcrumbs'][] = ['label' => $model->remedy->name, 'url' => ['/remedy/viewbyname', 'name' => $model->remedy->name]];
$this->params['breadcrumbs'][] = ['label' => '既製品レメディー', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name];
$this->params['breadcrumbs'][] = ['label' => '追加'];

?>
<div class="remedy-stock-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
