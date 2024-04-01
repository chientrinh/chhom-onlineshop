<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy/create.php $
 * $Id: create.php 969 2015-04-30 02:53:47Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Remedy */

$this->title = 'Create Remedy';
$this->params['breadcrumbs'][] = ['label' => "レメディー", 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="remedy-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
