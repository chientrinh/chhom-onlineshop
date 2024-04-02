<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/pointing/views/pointing_receipt.php $
 * $Id: receipt.php 3018 2016-10-23 09:05:52Z mori $
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
$this->registerCss($csscode);
?>

<page>
    <div>
        <span>
        <?= Html::a("印刷する", '#', ['class' => 'alert', 'onclick' => "window.print();history.back();return false;"]) ?>
        </span>
        <div style="float:right;text-align:right">
            <?php if($model->isModified()): ?>
                <?= Html::a("戻る", ['view','id'=>$model->pointing_id], ['class'=>'alert']) ?>
            <?php else: ?>
                <?= Html::a("次のお買い物", ['create'], ['class' => 'btn btn-success']) ?>
            <?php endif ?>
           <br>
           <br>
           <?= Html::a("無効",['expire','id' => $model->pointing_id],['class'=>'btn btn-danger']) ?>
           <br>
        </div>
    </div>

    <div id="receipt-<?= $model->pointing_id ?>" style="padding:0; margin-bottom:5px;">

       <p style="text-align:center"><strong><?= $title ?></strong></p>
       <div>
         <div style="width:50%;float:left"><?= Yii::$app->formatter->asDate($model->update_date,'php:Y年m月d日(D) H:i') ?></div>
         <div style="width:50%;float:right;text-align:right"><?= sprintf('%06d', $model->pointing_id) ?></div>
       </div>

       <p style="font-size:2pt">&nbsp;</p>
       <?php if($model->customer): $customer = $model->customer ?>
           <div>会員 <?= $customer->name ? $customer->name : "(未登録)" ?> 様</div>
           <div>
             <div style="width:50%;float:left;text-align:right">加算ポイント</div>
             <div style="width:50%;float:right;text-align:right">＋<?= number_format($model->point_given) ?></div>
           </div>
           <div>
               <div>
                 <div style="width:50%;float:left;text-align:right">現在ポイント</div>
                 <div style="width:50%;float:right;text-align:right">＝<?= number_format($customer->point) ?></div>
               </div>
           <div>
       <?php endif ?>

       <?php if(0 < strlen($model->note)): ?>
           <div>
             <div style="width:20%;float:left;text-align:left">備考</div>
             <div style="width:80%;float:right;text-align:left">
                 <?= Html::encode($model->note) ?>
             </div>
           </div>
       <?php endif ?>

       <span>&nbsp;<hr></span>

       <div><?= $model->company->name ?></div>
       <div><?= $model->company->addr ?></div>
       <div>TEL <?= $model->company->tel ?></div>
    </div>
</page>
