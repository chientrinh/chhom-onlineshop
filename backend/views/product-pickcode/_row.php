<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product-pickcode/_row.php $
 * $Id: _row.php 2483 2016-05-03 00:57:38Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\FileForm
 * @var $row   array of string
 */
$pname = ($m = $model->getModel()) ? $m->name : Html::tag('span',"商品が見つかりません",['class'=>'not-set']);

?>
<div>
<p>
<?= Html::tag('span', $row['ean13']       ,['class'=> $model->getDirtyAttributes(['ean13']) ? 'text-danger' : 'text-success']) ?>,
<?= Html::tag('span', $row['product_code'],['class'=> $model->getDirtyAttributes(['ean13']) ? 'text-danger' : 'text-success']) ?>,
<?= Html::tag('span', $row['pickcode']    ,['class'=> $model->getDirtyAttributes(['ean13']) ? 'text-danger' : 'text-success']) ?>,
<?= $pname ?>
</p>
<?= Html::errorSummary($model) ?>
</div>
