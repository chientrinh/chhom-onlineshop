<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/vegetable/create.php $
 * $Id: create.php 2931 2016-10-07 04:44:08Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Vegetable
 */

$this->params['breadcrumbs'][] = ['label' => '追加'];
?>
<div class="vegetable-create">

    <h1>野菜を追加</h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
