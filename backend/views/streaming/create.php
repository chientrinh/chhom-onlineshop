<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/streaming/create.php $
 * $Id: create.php 2286 2020-04-28 15:11:00Z kawai $
 */

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Streaming */

$this->params['breadcrumbs'][] = '追加';

?>
<div class="streaming-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
