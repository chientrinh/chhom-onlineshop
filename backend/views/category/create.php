<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/category/create.php $
 * $Id: create.php 2286 2016-03-21 06:11:00Z mori $
 */

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Category */

$this->params['breadcrumbs'][] = '追加';

?>
<div class="category-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
