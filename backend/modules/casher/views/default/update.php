<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/update.php $
 * $Id: update.php 3024 2016-10-27 02:56:12Z mori $
 *
 * $model \common\models\PurchaseForm
 */

use \yii\helpers\Html;

$payments = $this->context->module->getPayments();
$payments = \yii\helpers\ArrayHelper::map($payments, 'payment_id', 'name');

?>

<div class="dispatch-default-update">

    <p class="alert alert-danger">
        <strong>伝票NO <?= $model->purchase_id ?></strong> を編集中です
        <?= Html::a('中止',['apply','target'=>'barcode','barcode'=>'0'],['class'=>'btn btn-danger btn-xs']) ?>
    </p>

  <div class="body-content">

      <div class="col-md-12">
          <?= $this->render('_form',[
              'model'    => $model,
              'payments' => $payments,
          ]) ?>
      </div>

  </div>

</div>
