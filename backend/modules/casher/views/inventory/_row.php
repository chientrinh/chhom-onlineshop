<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/inventory/_row.php $
 * $Id: _row.php 2503 2016-05-12 09:38:58Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\InventoryItem
 */

?>
<div>
<p>
<?= Html::tag('span', $model->iitem_id) ?>,
<?= Html::tag('span', $model->ean13) ?>,
<?= Html::tag('span', $model->actual_qty) ?>
</p>
<?= Html::errorSummary($model) ?>
</div>
