<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/create.php $
 * $Id: create.php 2079 2016-02-13 06:03:39Z mori $
 *
 * $model \common\models\PurchaseForm
 */

use \yii\helpers\Html;

$payments = $this->context->module->getPayments();
$payments = \yii\helpers\ArrayHelper::map($payments, 'payment_id', 'name');

?>

<div class="dispatch-default-create">

  <div class="body-content">

      <div class="col-md-12">
          <?= $this->render('_form',[
              'model'    => $model,
              'payments' => $payments,
          ]) ?>
      </div>

  </div>

</div>
