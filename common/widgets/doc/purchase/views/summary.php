<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/purchase/views/summary.php $
 * $Id: summary.php 3745 2017-11-09 08:34:13Z naito $
 *
 * @var $this yii\web\View
 * @var $model common\models\Purchase
 * @var $attributes[] array which represents DetailView::attributes
 */

$formatter = Yii::$app->formatter;

// 熱海発送の商品のみ、sender（発送元）を熱海Branchにする
if(common\models\Branch::PKEY_ATAMI == $model->branch->branch_id)
{
    $sender = $model->branch;
    $sender->name = "豊受モール<br>".$sender->name;
}
else
{
    // 豊受自然農が発送元
    $sender = $model->sender;
}

?>

<div id="SummaryDocument">

  <h1> 納品書（合計表）</h1>

  <div>
    <p class="text-right"><?= date('Y 年 m 月 d 日', strtotime($model->update_date)) ?></p>

    <?php if(! $model->delivery): ?>
    <p class="alert alert-warning">この注文には配達先がありません</p>
    <?php endif ?>

    <div style="float:left;width:60%;height:36mm">
      <p>［お届け先］<br>
        <?php if($model->delivery): ?>
        〒<?= $model->delivery->zip ?><br>
        <?= $model->delivery->addr ?><br>
        <?= $model->delivery->name ?>様<br>
        TEL: <?= $model->delivery->tel ?>
        <?php else: ?>
        指定がありません
        <?php endif ?></p>
    </div>

    <div style="float:right;width:40%;height:36mm">
      <p>［発送者］<br>
        〒<?= $sender->zip ?><br>
        <?= $sender->addr ?><br>
        <?= $sender->name ?><br>
        TEL: <?= $sender->tel ?></p>
    </div>

    <div class="wrap" style="display:block;width:100%;height:32mm;">
      <div style="float:left;width:60%">
        <p>
        </p>
      </div>

      <div style="float:left;width:15%">
        <p>
          [注文番号]<br>
          [注文日時]
        </p>
      </div>
      <div style="float:right;width:25%" class="text-right">
        <p>
          <?= sprintf('%06d',$model->primaryKey) ?><br>
          <?= date('Y 年 m 月 d 日 H:i', strtotime($model->create_date)) ?>
        </p>
      </div>
    </div>

    <div class="wrap" style="float:none;width:100%">

      <p>
          このたびは豊受モールのご利用をありがとうございます。<br>
          ご注文の商品や送料・代引手数料につき以下のとおりご案内申し上げます。
      </p>

<?= \yii\widgets\DetailView::widget([
    'id'         => 'delivery-summary',
    'options'    => ['style' => 'width:60%; border:0; border-bottom: solid #D3D3D3'],
    'template'   => '<tr><td style="width:50%">{label}</td><td style="width:50%;text-align:right">{value}</td></tr>',
    'model'      => $model,
    'attributes' => $attributes,
]) ?>
      <p>
          またのご利用を心よりお待ちいたしております。
      </p>

<?php if($model->note): ?>

      <p>&nbsp;</p>
      <p>備考</p>
      <p>
        <?= $model->note ?>
      </p>

<?php endif ?>

    </div>
  </div>
</div>
