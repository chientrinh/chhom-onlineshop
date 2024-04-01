<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/sodan/views/karute/item.php $
 * $Id: item.php 1637 2015-10-11 11:12:30Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\webdb20\Karute
 */

?>
<div class="karute-view">

    <p>
        <strong class="text-success">
        <?= $model->syoho_date ?>
        <?= ! $model->consultationType ? null : $model->consultationType->name ?>
        <?= ! $model->branch ? null : $model->branch->name ?>
        <?= $model->homoeopath->name ?>
        </strong>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class'=>'table table-condensed'],
        'attributes' => [
           'user_report:ntext',
           'syoho_advice:ntext',
           'syoho_coment:ntext',
        ],
    ]) ?>

</div>
