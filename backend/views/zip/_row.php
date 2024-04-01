<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/zip/_row.php $
 * $Id: _row.php 2667 2016-07-07 08:26:14Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\InventoryItem
 */

?>
<div>

    <p>
        <?php foreach($model->attributes as $key => $value): ?>
            <?= Html::tag('span', $value) ?>,
        <?php endforeach ?>
    </p>

    <?= Html::errorSummary($model,['class'=>'alert alert-danger']) ?>

</div>
