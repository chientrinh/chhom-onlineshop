<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use \common\models\InventoryStatus;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/inventory/print.php $
 * $Id: print.php 2294 2016-03-24 05:59:55Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Inventory
 */

$inventory_id = sprintf('%06d', $model->inventory_id);
$this->params['breadcrumbs'][] = ['label' => $inventory_id];

$csscode = '
@media print {
@page { margin: 0.5cm; }
body {
  font-size: 9pt;
}
td {
  padding:0;
  margin: 0;
}
}
';
$this->registerCss($csscode);
?>
<div class="cashwer-inventory-print">

    <h2>棚卸票</h2>
    <?= $this->render('_view',[
        'model'      => $model, 
        'pagination' => false,
        'sort'       => false,
    ]) ?>

</div>
