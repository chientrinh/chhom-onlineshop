<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/streaming-buy/create.php $
 * $Id: create.php 2286 2016-03-21 06:11:00Z mori $
 */

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\StreamingBuy */

$this->params['breadcrumbs'][] = '追加';

?>
<div class="streaming-buy-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
