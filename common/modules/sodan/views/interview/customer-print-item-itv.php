<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/client/customer-print-item-itv.php $
 * $Id: customer-print-item-itv.php 1881 2015-12-16 14:52:21Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\webdb20\Karute
 */

?>
<div class="karute-view">

    <p>
        <strong class="text-success">
        <?= $model->itv_date ? date('Y-m-d', strtotime($model->itv_date)) : null ?>
        <?= $model->itv_time ? date('H:i', strtotime($model->itv_time)) : null ?>
        <?= $model->product ? $model->product->name  : null ?>
        <?= $model->homoeopath ? $model->homoeopath->homoeopathname : null ?>
        </strong>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class'=>'table table-condensed'],
        'attributes' => [
            'presence:ntext',
            'impression:ntext',
            'summary:ntext',
            'advice:ntext',
            'progress:ntext',
        ],
    ]) ?>

</div>
