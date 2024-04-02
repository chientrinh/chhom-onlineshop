<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/pointing/views/receipt.php $
 * $Id: receipt.php 4185 2019-09-30 16:12:44Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Pointing
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
@media print {
a { display:none }
body { padding: 0; font-size: 9pt; }
@page{
   margin-left:   5mm;
   margin-right:  5mm;
   margin-top:    0px;
   margin-bottom: 0px;
   width:        80mm;
   }
}
";

$subtotal = 0;
$tax = 0;
$reduced_subtotal = 0;
$reduced_tax = 0;


$this->registerCss($csscode);

?>

<page>

<div>
 <span>
 <?= Html::a("印刷する",'#',['class'=>'alert', 'onclick'=>"window.print();history.back();return false;"]) ?>
 </span>
 <div style="float:right;text-align:right">
     <?php if($model->isModified()): ?>
         <?= Html::a("戻る",['view','id'=>$model->pointing_id],['class'=>'alert']) ?>
     <?php else: ?>
         <?= Html::a("次のお買い物",['create'],['class'=>'btn btn-success']) ?>
     <?php endif ?>
    <br>
    <br>
<!-- // OPERATIONS -->
    <?= Html::a("修正",['update','id'=>$model->pointing_id],['class'=>'btn btn-primary']) ?>
    <br>
    <?= Html::a("無効",['expire','id'=>$model->pointing_id],['class'=>'btn btn-danger']) ?>
    <br>
 </div>
</div>

<div id="receipt-<?= $model->pointing_id ?>" style="padding:0; margin-bottom:5;">

 <p style="text-align:center">
    <strong>
    <?= $title ?>
    </strong>
 </p>

<div>
  <div style="width:50%;float:left">
    <?= Yii::$app->formatter->asDate($model->update_date,'php:Y年m月d日(D) H:i') ?>
  </div>
  <div style="width:50%;float:right;text-align:right">
    <?= sprintf('%06d', $model->pointing_id) ?>
  </div>

</div>

<span>&nbsp;<hr></span>

<?php foreach($model->items as $item) {
    if($item->isReducedTax()) {
        $reduced_subtotal += ($item->price + $item->unit_tax) * $item->quantity;
        $reduced_tax += $item->unit_tax * $item->quantity;
    } else {
        $subtotal += ($item->price + $item->unit_tax) * $item->quantity;
        $tax += $item->unit_tax * $item->quantity;
    }

 ?>
<div>

  <div style="width:20%;float:left">
  <?= $item->code ?>
  </div>
  <div style="width:80%;float:right">
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
  @<?= number_format($item->price) ?> × <?= $item->quantity ?>点
  </div>

  <div style="width:50%;float:right;text-align:right">
  ￥<?= number_format($item->basePrice) ?>
  </div>

</div>
<?php } ?>



<span>&nbsp;<hr></span>

<!-- // TAXABLE -->
<div>
  <div style="width:50%;float:left;text-align:left">
  小計 <?= $model->itemCount ?> 点
  </div>
  <div style="width:50%;float:right;text-align:right">
  ￥<?= number_format($model->taxable) ?>
  </div>
</div>

<!-- // TAX -->
<div>
  <div style="width:50%;float:left;text-align:left">
  消費税
  </div>
  <div style="width:50%;float:right;text-align:right">
  ￥<?= number_format($model->tax) ?>
  </div>
</div>


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
  <div style="width:50%;float:left;text-align:left">
    <strong>
    合計
    </strong>
  </div>
  <div style="width:50%;float:right;text-align:right">
    <strong>
    ￥<?= number_format($model->total_charge) ?>
    </strong>
  </div>
</div>

<?php
// 軽減税率スタート2019/10/01 に合わせて表示内容を変更する
    if (strtotime($model->create_date) >= \common\models\Tax::newDate()) {
?>        
<div>
  <div style="width:50%;float:left;text-align:left">
    <strong>
    10%対象
    </strong>
  </div>
  <div style="width:50%;float:right;text-align:right">
    <strong>
    ￥<?= number_format($subtotal) ?>
    </strong>
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
    <strong>
    8%対象
    </strong>
  </div>
  <div style="width:50%;float:right;text-align:right">
    <strong>
    ￥<?= number_format($reduced_subtotal) ?>
    </strong>
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
<br>
    <strong>
   「※」は軽減税率対象
    </strong>
  </div>
</div>
<?php } ?>

<p style="font-size:2pt">&nbsp;</p>

<!-- // RECEIVED  -->
<div class="alert alert-success">
  <div style="width:50%;float:left;text-align:left">
  お預り
  </div>
  <div style="width:50%;float:right;text-align:right">
    ￥<?= number_format($model->receive) ?>
  </div>
</div>

<!-- // CHANGE  -->
<div class="alert alert-error">
  <div style="width:50%;float:left;text-align:left">
  おつり
  </div>
  <div style="width:50%;float:right;text-align:right">
    ￥<?= number_format($model->change) ?>
  </div>
</div>

<!-- // NOTE  -->
<?php if(0 < strlen($model->note)): ?>
<div>
  <div style="width:20%;float:left;text-align:left">
  備考
  </div>
  <div style="width:80%;float:right;text-align:left">
      <?= Html::encode($model->note) ?>
  </div>
</div>
<?php endif ?>

<p style="font-size:2pt">&nbsp;</p>

<?php if($model->customer): $customer = $model->customer ?>

<div>
会員 <?= $customer->name ? $customer->name : "(未登録)" ?> 様
</div>

<div>
  <div style="width:50%;float:left;text-align:right">
  使用ポイント 
  </div>
  <div style="width:50%;float:right;text-align:right">
  ー<?= number_format($model->point_consume) ?>
  </div>
</div>

<div>
  <div style="width:50%;float:left;text-align:right">
  加算ポイント
  </div>
  <div style="width:50%;float:right;text-align:right">
  ＋<?= number_format($model->point_given) ?>
  </div>
</div>
<div>

<div>
  <div style="width:50%;float:left;text-align:right">
  現在ポイント
  </div>
  <div style="width:50%;float:right;text-align:right">
  ＝<?= number_format($customer->point) ?>
  </div>
</div>
<div>

<?php endif ?>

<span>&nbsp;<hr></span>

<div>
  <?= $model->seller->name ?>
</div>

<div>
<?= $model->seller->addr ?>
</div>

<div>
TEL <?= $model->seller->tel ?>
</div>

<p style="font-size:8pt"> 当店では税抜価格の小計に対して、消費税を計算してご請求しております。</p>
</div> <!-- id='receipt' -->

</page>
