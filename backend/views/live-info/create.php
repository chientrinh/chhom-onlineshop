<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/live-info/create.php $
 * $Id: create.php 2286 2020-04-28 15:11:00Z kawai $
 */

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\LiveInfo */

$this->params['breadcrumbs'][] = '追加';

?>
<div class="live-info-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
