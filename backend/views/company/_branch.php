<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/company/_branch.php $
 * $Id: _branch.php 804 2015-03-19 07:31:58Z mori $
 *
 * $model: common\models\Branch
 */
?>

<div>
<h3><?= Html::a($model->name, yii\helpers\Url::toRoute(['/branch/view', 'id'=>$model->branch_id])) ?></h3>
<p>
  〒<?= $model->zip ?>
  <?= $model->addr ?>
  <?= $model->tel ?>
</p>
</div>