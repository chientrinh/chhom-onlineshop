<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/remedy/views/label-machine.php $
 * $Id: label-machine.php 1940 2016-01-06 02:48:38Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\MachineRemedyForm
 * @var $title string
 */

$p1 = \common\models\RemedyPotency::findOne($model->potency1);
$p2 = \common\models\RemedyPotency::findOne($model->potency2);

?>
<Table style="page-break-after:always"></table>
<span style="line-height:7.0pt;font-size:7.0pt">
<?= $title ?>
<br>
特別レメディー
<br>
</span>
<?php if($model->abbr1): ?>
<span style="line-height:7.0pt;font-size:7.0pt">
+<?= $model->abbr1 ?> <?= $p1 ? $p1->name : null ?>
</span><br>
<?php endif ?>
<?php if($model->abbr2): ?>
<span style="line-height:7.0pt;font-size:7.0pt">
+<?= $model->abbr2 ?> <?= $p2 ? $p2->name : null ?>
</span><br>
<?php endif ?>
