<?php

use yii\helpers\Html;
use yii\helpers\Json;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product-master/_row.php $
 * $Id: _row.php 2695 2016-07-10 06:23:09Z mori $
 *
 * @var $this  yii\web\View
 * @var $model common\models\ProductMaster
 * @var $row   array of string
 */

?>
<div class="alert <?= $model->hasErrors() ? 'alert-danger' : 'alert-success' ?>">

    <p>
        csv:<?= Json::encode($row) ?>
    </p>

    <?php if(! $model->isNewRecord): ?>
    <p>
        now:<?= Json::encode($model->getAttributes(['ean13','name','dsp_priority'])) ?>
    </p>
    <?php endif ?>

    <?= Html::errorSummary($model) ?>

</div>
