<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/views/receipt.php $
 * $Id: receipt.php 4200 2019-11-08 07:33:42Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Purchase
 * @var $title string
 */

$csscode = "
@media screen {
  .btn           { display:inline-block; padding: 6px 12px; border-box; border-radius: 4px; text-decoration: none; }
  .btn-success   { color: #fff; background-color: #5cb85c; border-color: #4cae4c; }
  .btn-warning   { color: #fff; background-color: #f0ad4e; border-color: #eea236; }
  .btn-primary   { color: #fff; background-color: #337ab7; border-color: #2e6da4; }
  .btn-danger    { color: #fff; background-color: #d9534f; border-color: #d43f3a; }
  .alert         { font-size: 30pt; font-weight: bold; text-shadow: 0 1px 0 rgba(255,255,255,0.5); }
  .alert-success { color: #468847; background-color: #dff0d8;}
  .alert-error   { color: #b94a48; background-color: #f2dede;}
text-shadow: 
background-color: 
border: 1px solid #fbeed5;
}
table {
   border: 0px;
   border-collapse: collapse;
}
div,hr,p,table {
box-sizing:border-box;
-moz-box-sizing:border-box;
-webkit-box-sizing:border-box;
}
hr {
   border-top: 1px solid #000000;
   width: 100%;
}
p, td { padding: 0 }

@media print {
body  { font-size: 9pt }
a     { display:none   }
@page {
   margin-left:   5mm;
   margin-right:  5mm;
   margin-top:    0px;
   margin-bottom: 0px;
   width:        80mm;
   }
}

.square {
   width:         20mm;
   height:        20mm;
   border-style:  solid;
   border-width:  thin;
   float:         right;
   margin-top:    20px;
   margin-bottom: 20px;
   padding-top:   7mm;
   padding-left:  5mm;
}
";

$need_item_calc = false;
$subtotal = 0;
$tax = 0;
$reduced_subtotal = 0;
$reduced_tax = 0;
$taxed_subtotal = $model->taxedSubtotal;

$this->registerCss($csscode);

if('casher' == Yii::$app->controller->module->id)
    $backBtn = Html::a("次のお買い物",['create'],['title'=>'実店舗レジに戻ります','class'=>'btn btn-success']);
else
    $backBtn = Html::a("戻る",'#',['onclick'=>'history.back();return false;','class'=>'alert']);
?>

<page>
    <div>
        <?= Html::a("印刷",'#',['class'=>'alert', 'tabstop'=>1, 'onclick'=>"window.print();return false;"]) ?>
    </div>
    <div style="float:right;text-align:right">
     <?= $backBtn ?>
<br>
<br>
<br>
<?= Html::a("修正",['/casher/default/update','id'=>$model->purchase_id],['class'=>'btn btn-primary']) ?>
<br>
<?= Html::a("取消",['/purchase/cancel','id'=>$model->purchase_id],['class'=>'btn btn-danger']) ?>
</div>

<div id="receipt-<?= $model->purchase_id ?>" style="padding:0; margin-bottom:5;">

 <p style="text-align:center">
    <strong>
    <?= $title ?>
    </strong>
 </p>

<div>
  <div style="width:50%;float:left">
    <?= Yii::$app->formatter->asDate($model->create_date,'php:Y年m月d日(D) H:i') ?>
    <?php if($model->create_date != $model->update_date): ?>
        起票<br>
      <?= Yii::$app->formatter->asDate($model->update_date,'php:Y年m月d日(D) H:i') ?> 修正
    <?php endif ?>
  </div>
  <div style="width:50%;float:right;text-align:right">
    <?= sprintf('%06d', $model->purchase_id) ?>
  </div>
</div>

<span>&nbsp;<hr></span>

<?php
 //$need_item_calc = true;

 if($model->tax10_price == 0 && $model->tax8_price == 0 && $model->taxHP_price == 0) {
     $need_item_calc = true;
 } else {
     $reduced_subtotal = $model->tax8_price;
     $reduced_tax = floor($model->tax8_price / (\common\models\Tax::findOne(2)->getRate()+100) * \common\models\Tax::findOne(2)->getRate());
     $subtotal = $model->tax10_price + $model->taxHP_price;
     $tax = floor($subtotal / (\common\models\Tax::findOne(1)->getRate()+100) * \common\models\Tax::findOne(1)->getRate());
     $taxed_subtotal = $subtotal + $reduced_subtotal + $model->point_consume;
 }


 foreach($model->items as $item){
//var_dump($item->isReducedTax());
    if($need_item_calc) {
        if($item->isReducedTax()) {
            $reduced_subtotal += ($item->unit_price + $item->unit_tax) * $item->quantity;
            $reduced_tax += $item->unit_tax * $item->quantity;
        } else {
            $subtotal += ($item->unit_price + $item->unit_tax) * $item->quantity;
            $tax += $item->unit_tax * $item->quantity;
        }
    }

 ?>
<div>

  <div style="width:100%">
  <?php
        // 軽減税率スタート2019/10/01 に合わせて表示内容を変更する
        if (strtotime($model->create_date) < \common\models\Tax::newDate()) {
            echo $item->name;
        } else {
            // 一旦無条件で軽減税率対象とみなす
            // TODO: 税率判定処理をここに入れる
            echo $item->isReducedTax() ? $item->name.'※' : $item->name;
        }
  ?>
  </div>

  <div style="width:50%;float:left;text-align:right">
  @<?= number_format($item->unit_price + $item->unit_tax) ?> × <?= $item->qty ?>点
  </div>


  <div style="width:50%;float:right;text-align:right">
  ￥<?= number_format($item->taxPrice) ?>
  </div>

  <?php if($item->discountRate): ?>
  <?php if($model->branch->branch_id != \common\models\Branch::PKEY_EVENT): ?>
  <div style="width:50%;float:left;text-align:right">
  値引 <?= $item->discountRate ?>％
  </div>

  <div style="width:50%;float:right;text-align:right">
  −<?= number_format($item->discountTotal) ?>
  </div>
  <?php else: ?>
  <div style="width:100%;float:left;text-align:right">
  値引 <?= $item->discountRate ?>％
  </div>
  <?php endif ?>
  <?php endif ?>

</div>
<?php } ?>

<span>&nbsp;<hr></span>

<!-- // TAXABLE -->
<div>
  <div style="width:50%;float:left;text-align:left">
  小計 <?= $model->itemCount ?> 点
  </div>
  <div style="width:50%;float:right;text-align:right">
  ￥<?= number_format($model->taxedSubtotal) ?>
  </div>
</div>

<!-- // TAX -->
<?php
// 軽減税率スタート2019/10/01 に合わせて表示内容を変更する
if (strtotime($model->create_date) < \common\models\Tax::newDate()) {
?>
<div>
  <div style="width:50%;float:left;text-align:left">
  （内消費税等
  </div>
  <div style="width:50%;float:right;text-align:right">
  ￥<?= number_format($model->tax) ?>）
  </div>
</div>
<?php } ?>

<!-- // POSTAGE -->
<?php if(0 < $model->postage): ?>
<div>
  <div style="width:50%;float:left;text-align:left">
  送料
  </div>
  <div style="width:50%;float:right;text-align:right">
  ￥<?= number_format($model->postage) ?>
  </div>
</div>
<?php endif ?>

<!-- // HANDLING -->
<?php if(0 < $model->handling): ?>
<div>
  <div style="width:50%;float:left;text-align:left">
  送料
  </div>
  <div style="width:50%;float:right;text-align:right">
  ￥<?= number_format($model->handling) ?>
  </div>
</div>
<?php endif ?>

<!-- // DISCOUNT -->
<?php if($model->discount): ?>
<div>

  <div style="width:50%;float:left;text-align:left">
  値引き
  </div>

  <div style="width:50%;float:right;text-align:right">
  ￥<?= number_format(0 - $model->discount) ?>
  </div>

</div>
<?php endif ?>

<!-- // POINT_CONSUME -->
<?php if($model->point_consume): ?>
<div>

  <div style="width:50%;float:left;text-align:left">
  ポイント値引
  </div>

  <div style="width:50%;float:right;text-align:right">
  <?= number_format(0 - $model->point_consume) ?>
  </div>

</div>
<?php endif ?>

<!-- // TOTAL CHARGE -->
<div>
  <div style="width:50%;float:left;text-align:left;font-size:20pt">
    <strong>
    合計
    </strong>
  </div>
  <div style="width:50%;float:right;text-align:right;font-size:20pt">
    <strong>
    ￥<?= number_format($model->total_charge) ?>
    </strong>
  </div>
</div>
<?php
// 軽減税率スタート2019/10/01 に合わせて表示内容を変更する
    if (strtotime($model->create_date) >= \common\models\Tax::newDate()) {
?>
<hr style="border-top:dotted 2px #cccccc;">
<div>
  <div style="width:50%;float:left;text-align:left">
    税率10%対象
  </div>
  <div style="width:50%;float:right;text-align:right">
    ￥<?= number_format($subtotal) ?>
  </div>
</div>
<div>
  <div style="width:50%;float:left;text-align:left">
  （内消費税
  </div>
  <div style="width:50%;float:right;text-align:right">
  ￥<?= number_format($tax) ?>）
  </div>
</div>
<div>
  <div style="width:50%;float:left;text-align:left">
    税率8%対象
  </div>
  <div style="width:50%;float:right;text-align:right">
    ￥<?= number_format($reduced_subtotal) ?>
  </div>
</div>
<div>
<div>
  <div style="width:50%;float:left;text-align:left">
  （内消費税
  </div>
  <div style="width:50%;float:right;text-align:right">
  ￥<?= number_format($reduced_tax) ?>）
  </div>
</div>
  <div style="width:100%;float:left;text-align:left">
</div>
<?php } ?>
<p style="font-size:2pt">&nbsp;</p>

<hr style="border-top:dotted 2px #cccccc;">
<!-- // PAYMENT  -->
<div>
  <div style="width:50%;float:left;text-align:left">
  お支払い
  </div>
  <div style="width:50%;float:right;text-align:right">
  <?= $model->payment->name ?>
  </div>
</div>

<!-- // RECEIVED  -->
<div class="alert alert-success">
  <div style="width:50%;float:left;text-align:left;font-size:18pt">
  お預り
  </div>
  <div style="width:50%;float:right;text-align:right;font-size:18pt">
    ￥<?= number_format($model->receive) ?>
  </div>
</div>

<!-- // CHANGE  -->
<div class="alert alert-error">
  <div style="width:50%;float:left;text-align:left;font-size:18pt">
  おつり
  </div>
  <div style="width:50%;float:right;text-align:right;font-size:18pt">
    ￥<?= number_format($model->change) ?>
  </div>
</div>
  <div style="width:100%;float:left;text-align:left">
<br>
   「※」は軽減税率対象
  </div>

<p style="font-size:2pt">&nbsp;</p>

<?php if($model->customer): $customer = $model->customer ?>

<div>
<!--会員 <?= $customer->name ? $customer->name : "(未登録)" ?> 様-->
会員証NO  <?= $customer->code ? $customer->code : "(未登録)" ?><br />
<?php if ($print_name_flg == 'true') { ?>
<?="顧客名　". ($customer->name ? $customer->name : "(未登録)") ?>&nbsp;様
<?php } ?>
</div>

<div>
  <div style="width:50%;float:left;text-align:right">
  使用ポイント 
  </div>
  <div style="width:50%;float:right;text-align:right">
  −<?= number_format($model->point_consume) ?>
  </div>
</div>

<div>
  <div style="width:50%;float:left;text-align:right">
  加算ポイント
  </div>
  <div style="width:50%;float:right;text-align:right">
  +<?= number_format($model->point_given) ?>
  </div>
</div>
<div>

<div>
  <div style="width:50%;float:left;text-align:right">
  現在ポイント
  </div>
  <div style="width:50%;float:right;text-align:right">
  =<?= number_format($customer->point) ?>
  </div>
</div>
<div>

<?php elseif (($model->canGetProperty('recipe') && $recipe = $model->recipe) && $print_name_flg == 'true' && ($recipe->manual_client_name)): ?>
<div><?="顧客名　". $recipe->manual_client_name ?>&nbsp;様</div>
<?php endif ?>

<div class="square">印 紙</div>    
    
<span>&nbsp;<hr></span>

<div>
  <?= $model->branch->name ?>
</div>

<div>
<?= $model->branch->addr ?>
</div>

<div>
TEL <?= $model->branch->tel ?>
</div>

<p style="font-size:8pt"> 商品価格には消費税等含みます。</p>
</div> <!-- id='receipt' -->

<!-- // OPERATIONS -->
</page>
