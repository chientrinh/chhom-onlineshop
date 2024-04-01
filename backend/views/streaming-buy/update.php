<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/streaming-buy/update.php $
 * $Id: update.php 2286 2020-04-28 12:11:00Z kawai $
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\StreamingBuy */

$this->params['breadcrumbs'][] = ['label' => $model->streaming_buy_id, 'url' => ['view', 'id' => $model->streaming_buy_id]];
$this->params['breadcrumbs'][] = '編集';
?>
<div class="streaming-buy-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
