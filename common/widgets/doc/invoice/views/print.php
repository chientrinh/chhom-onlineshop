<?php

use yii\helpers\Html;
use \common\models\Company;
use \common\models\Payment;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/widgets/doc/invoice/views/print.php $
 * $Id: print.php 4185 2019-09-30 16:12:44Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Invoice
 */

$formatter = Yii::$app->formatter;

$customer = $model->customer;
$office   = $customer->office;

$tax = $model->computeTax();
?>

<?php if($model->hasErrors() || $model->isVoid()): ?>
<div class="alert alert-danger">
この請求書は最新の状態を反映していません。再発行が必要です。
<?php foreach($model->firstErrors as $attr => $msg): ?>
<blockquote>
  <p>
    <?= $model->getAttributeLabel($attr) ?>
      <small><?= $msg ?></small>
  </p>
</blockquote>
<?php endforeach ?>
<?= Html::a('再発行する',['/invoice/admin/update','year'=>$model->year,'month'=>$model->month,'customer_id'=>$model->customer_id],['class'=>'btn btn-warning']) ?>
</div>
<?php endif ?>

<page>

    <div style="position: absolute; top:35mm; right:12mm;">
        <img width="100" height="100" alt="stamp" src="<?= $stamp ?>">
    </div>

<htmlpagefooter name="myFooter" style="display:none">
<table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 8pt; 
    color: #000000; font-weight: bold; font-style: italic;"><tr>
    <td width="33%" style="font-weight:normal" class="text-left"><?= sprintf('%06d', $model->invoice_id)?></td>
    <td width="33%" style="font-weight:normal" class="text-center">{PAGENO}/{nbpg}</td>
    <td width="33%" style="font-weight:normal" class="text-right"><?= sprintf('%04d-%02d', $model->year, $model->month)?> </td>
    </tr></table>
</htmlpagefooter>

  <div>
    <p class="text-right">
        発行日 <?= date('Y 年 m 月 t 日', strtotime($model->target_date)) ?><br>
        <?= $model->getAttributeLabel('invoice_id') ?> <?= sprintf('%06d',$model->invoice_id) ?>
    </p>

    <p class="text-center">
        <?= $model->year ?>年 <?= $model->month?>月度 ご請求書
    </p>

    <div style="float:left;width:58%;height:36mm">
        <p>
            <?php if($office): ?>
                〒<?= $office->zip ?><br>
                <?= $office->addr ?><br>
                <?php if(isset($office->company_name) && 0 < strlen($office->company_name)) {
                         if(isset($office->person_name) && 0 < strlen($office->person_name)){
                            if(str_replace(array(" ", "　"), "", $office->company_name) == str_replace(array(" ", "　"), "", $office->person_name)) {
                                 echo $office->person_name." 様<br>";
                            } else {
                                echo $office->company_name.'<br>';
                                echo $office->person_name.' 様<br>';
                            }
                         } else {
                             echo $office->company_name." 御中<br>";
                         }
                      }
                ?>
            <?php else: ?>
                〒<?= $customer->zip ?><br>
                <?= $customer->addr ?><br>
                <?= $customer->name ?> 様<br>
          <?php endif ?>
      </p>
    </div>

    <div style="float:right;width:42%;height:36mm">

      <p>
        <br>
        <?= $company->name ?><br>
        本社:<br>
        〒<?= $company->zip ?><br>
        <?= $company->addr ?><br><br>
        連絡先（東京連絡事務所）:<br>
        〒158-0096<br>
        東京都世田谷区玉川台2-2-3<br>
        TEL: 03-5797-3371  FAX: 03-5797-3372<br>
        E-Mail: member@toyouke.com</p>
    </div>

    <div class="wrap" style="display:block;width:100%;">
      <div style="float:left;width:60%">
        <p>
        </p>
      </div>
      <div style="float:right;width:40%" class="text-right">
        <p>
            お支払方法: <?= $model->payment->name ?></p>
      </div>
    </div>

    <div class="wrap" style="float:none;width:100%">
        <p>
            下記のとおり、ご請求申し上げます。
        </p>
        <p class="h3" style="text-decoration: underline">
            <?= $model->getAttributeLabel('due_total') ?>
            <span id="due_total"><?= $formatter->asCurrency($model->due_total) ?></span>
        </p>

        <div class="row">
            <div class="col-md-4">
                &nbsp;
               <?php if($customer->isAgency() || $model->due_pointing || $model->due_commission): ?>
                <?= \yii\widgets\DetailView::widget([
                    'id'    => 'invoice-summary',
                    'model' => $model,
                    'template' => '<tr><th>{label}</th>{value}</tr>',
                    'options'  => ['class'=>'table table-condensed', 'style' => 'width:50%;'],
                    'attributes' => [
                        [
                            'attribute' => 'due_purchase',
                            'format'    => 'raw',
                            'value'     => call_user_func(function($data) use ($model,$tax,$formatter) { return '<td align="right">'.$formatter->asCurrency($model->due_purchase).'</td>'; }, $model),
                        ],
                        [
                            'attribute' => 'due_pointing',
                            'format'    => 'raw',
                            'value'     => call_user_func(function($data) use ($model,$tax,$formatter) { return '<td align="right">'.$formatter->asCurrency($model->due_pointing).'</td>'; }, $model),
                        ],
/*
                        [
                            'attribute' => 'due_tax',
                            'value'     => call_user_func(function($data) use ($tax) { return $tax['tax_total']; }, $model),
                            'format'    => 'currency',
                            'visible'   => strtotime($model->target_date) >= \common\models\Tax::newDate() ? true : false,
                        ],
                        [
                            'attribute' => 'due_normal_subtotal',
                            'value'     => call_user_func(function($data) use ($tax) { return $tax['normal_subtotal']; }, $model),
                            'format'    => 'currency',
                            'visible'   => strtotime($model->target_date) >= \common\models\Tax::newDate() ? true : false,
                        ],
*/
                        [
                            'attribute' => 'due_normal_subtotal',
                            'value'     => call_user_func(function($data) use ($tax, $formatter) { return  '<td align="right">'.$formatter->asCurrency($tax['normal_subtotal']).'</td><td>（内消費税 '.$formatter->asCurrency($tax['normal']).')</td>'; }, $model),
                            'format'    => 'raw',
                            'contentOptions' => ['class' => 'bg-red'],
                            'template'  => '<tr><th>{label}</th>
<td align="right">{value}</td><td>（内消費税　\）</td></tr>',
                            'visible'   => strtotime($model->target_date) >= \common\models\Tax::newDate() ? true : false,
                        ],
                        [
                            'attribute' => 'due_reduced_subtotal',
                            'value'     => call_user_func(function($data) use ($tax, $formatter) { return  '<td align="right">'.$formatter->asCurrency($tax['reduced_subtotal']).'</td><td>（内消費税 '.$formatter->asCurrency($tax['reduced']).')</td>'; }, $model),
                            'format'    => 'raw',
                            'visible'   => strtotime($model->target_date) >= \common\models\Tax::newDate() ? true : false,
                        ],
/*
                        [
                            'attribute' => 'due_reduced_subtotal',
                            'value'     => call_user_func(function($data) use ($tax) { return $tax['reduced_subtotal']; }, $model),
                            'format'    => 'currency',
                            'visible'   => strtotime($model->target_date) >= \common\models\Tax::newDate() ? true : false,
                        ],
                        [
                            'attribute' => 'due_reduced_tax',
                            'label'     => '(内消費税',
                            'value'     => call_user_func(function($data) use ($tax, $formatter) { return $formatter->asCurrency($tax['reduced']).')'; }, $model),
                            'format'    => 'html',
                            'visible'   => strtotime($model->target_date) >= \common\models\Tax::newDate() ? true : false,
                        ],
*/
                    ],
                ]) ?>
            <?php endif ?>
            </div>
            <div class="col-md-4" style="font-size: 8pt;"><p>※ポイント付与代金 = ポイント付与（貴社ご負担分） - ポイント使用</p></div>
            <div class="col-md-4">
                <?php if(Payment::PKEY_BANK_TRANSFER == $model->payment_id): ?>
                    <p>
                        お支払は、下記銀行口座にお振込くださいますようお願い申し上げます。
                    </p>
                    <p>
                        ■お振込先<br />
                        三井住友銀行　渋谷駅前支店（普通） 4932649　日本豊受自然農株式会社
                    <p>
                        ■お支払い期日<br />
                            <?php if(Payment::PKEY_DIRECT_DEBIT != $model->payment->payment_id): ?>
                                    <?= date('Y', strtotime($model->due_date)) ?>年
                                    <?= date('m', strtotime($model->due_date)) ?>月
                                    <?= date('d', strtotime($model->due_date)) ?>日<br>
                            <?php endif ?>
                    </p>
                    <p>
                        恐れ入りますが、振込手数料はご負担下さい。<br />
                        なお、お振込みの際には、「振込依頼人名」欄に、お名前とあわせて、請求書番号（請求書右上記載）の入力をお願いいたします。
                    </p>
                <?php else: ?>
                    <p>
                        <!-- ご指定の口座へ当月20日に振込させていただきます。 -->
                        口座振替日：<?= date('Y 年 m 月 26 日', strtotime(date('Y-m-1',strtotime($model->target_date)).' +1 month')) ?><br>
                        ※26日が金融機関休業日の場合は翌営業日が振替日となります。
                    </p>
                <?php endif ?>
            </div>
        </div>
<br />
        <p><strong>■内訳（伝票別）</strong></p>
        <?= \yii\grid\GridView::widget([
          'id' => 'grid-purchase',
          'dataProvider' => new \yii\data\ActiveDataProvider([
              'query'      => $model->getPurchases(),
              'pagination' => false,
              'sort'       => false,
          ]),
          'tableOptions' => ['class'=>'table table-striped table-condensed'],
          'emptyText' => '履歴はありません',
          'showOnEmpty' => false,
          'showFooter'  => false,
          'layout'      => '{items}',
          'columns'     => [
              [
                  'label' => '注文日',
                  'value'=> function($data){ return $data->create_date; },
                  'format'   => 'date',
                  'footerOptions' => ['class' => 'text-right info'],
                  'headerOptions' => ['style' => 'width:12%; font-weight: normal;'],
              ],
              [
                  'attribute' => 'purchase_id',
                  'value'     => function($data){ return sprintf('%06d',$data->purchase_id); },
                  'footerOptions' => ['class' => 'text-right info'],
                  'headerOptions' => ['style' => 'width:10%; font-weight: normal;'],
              ],
              [
                  'attribute'=> 'subtotal',
                  'format'   => 'integer',
                  //'footer'   => $formatter->asCurrency($model->getPurchases()->sum('subtotal')),
                  'contentOptions' => ['class' => 'text-right'],
                  'footerOptions' => ['class' => 'text-right info'],
                  'headerOptions' => ['style' => 'width:14%; text-align:right; font-weight: normal;'],
              ],
              [
                  'attribute'=> 'tax',
                  'format'   => 'integer',
                  //'footer'   => $formatter->asCurrency($model->getPurchases()->sum('tax')),
                  'contentOptions' => ['class' => 'text-right'],
                  'footerOptions' => ['class' => 'text-right info'],
                  'headerOptions' => ['style' => 'width:9%; text-align:right; font-weight: normal;'],
              ],
              [
                  'label'    => '送料等',
                  'format'   => 'integer',
                  'value'    => function($data){ return ($data->handling + $data->postage - $data->discount); },
                  //'footer'   => $formatter->asCurrency($model->getPurchases()->sum('handling + postage - discount')),
                  'contentOptions' => ['class' => 'text-right'],
                  'footerOptions' => ['class' => 'text-right info'],
                  'headerOptions' => ['style' => 'width:10%; text-align:right; font-weight: normal;'],
              ],
              [
                  'attribute'=> 'point_consume',
                  'format'   => 'integer',
                  'value'    => function($data){ return (0 - abs($data->point_consume)); },
                  //'footer'   => $formatter->asCurrency($model->getPurchases()->sum('point_consume')),
                  'contentOptions' => ['class' => 'text-right'],
                  'footerOptions' => ['class' => 'text-right info'],
                  'headerOptions' => ['style' => 'width:9%; text-align:right; font-weight: normal;'],
              ],
              [
                  'label'    => '合計金額',
                  'attribute'=> 'total_charge',
                  'format'   => 'integer',
                  //'footer'   => $formatter->asCurrency($model->getPurchases()->sum('total_charge')),
                  'contentOptions' => ['class' => 'text-right'],
                  'footerOptions' => ['class' => 'text-right info'],
                  'headerOptions' => ['style' => 'width:18%; text-align:right; font-weight: normal;'],
              ],
          ],
      ]); ?>


<!-- #119 【10月度】請求書PDF改修　対応にてコメントアウト
      <?php //if($model->getPurchases()->exists()): ?>
      <p><strong>■内訳（販売会社別）</strong></p>
      <?php /*echo \yii\grid\GridView::widget([
          'dataProvider' => new \yii\data\ActiveDataProvider([
              'query'      => Company::find()->andWhere([
                  'company_id' => [Company::PKEY_TY,
                                   Company::PKEY_HE,
                                   Company::PKEY_HJ,
                                   Company::PKEY_HP,]]),
              'pagination' => false,
              'sort'       => false,
          ]),
          'tableOptions' => ['class'=>'table table-striped table-condensed'],
          'emptyText'    => '内訳はありません',
          'layout'       => '{items}',
          'columns'      => [
              [
                  'label'     => '販売会社名',
                  'attribute' => 'name',
                  'headerOptions' => ['style' => 'font-weight: normal;'],
              ],
              [
                  'label' => '金額',
                  'value' => function($data) use ($model, $formatter)
                  {
                      $query = \common\models\PurchaseItem::find()->andWhere([
                          'purchase_id' => $model->getPurchases()->select('purchase_id')
                      ]);
                      $query->andWhere(['company_id' => $data->company_id]);

                      return $formatter->asCurrency((int)$query->sum('(price - discount_amount) * quantity'));
                  },
                  'contentOptions' => ['class'=>'text-right'],
                  'headerOptions' => ['class'=>'text-right', 'style' => 'font-weight: normal;'],
              ],
          ],
      ])*/ ?>
      <?php //endif ?>-->

      <?php if($customer->isAgency() && $model->getPointings()->exists()): ?>
      <p><strong>■販売店様売上（ポイント付与代金）</strong></p>
      <?= \yii\grid\GridView::widget([
          'id' => 'grid-pointing',
          'dataProvider' => new \yii\data\ActiveDataProvider([
              'query'      => $model->getPointings(),
              'pagination' => false,
              'sort'       => false,
          ]),
          'tableOptions' => ['class'=>'table table-striped table-condensed'],
          'emptyText' => '履歴はありません',
          'layout'      => '{items}',
          'showFooter'  => true,
          'columns'     => [
              [
                  'attribute'      => 'create_date',
                  'label'          => '売上日　　　　　',
                  'format'         => 'date',
                  'headerOptions'  => ['style' => 'font-weight: normal;'],
              ],
              [
                  'attribute'      => 'pointing_id',
                  'label'          => '注文番号　　　　',
                  'value'          => function($data){ return sprintf('%06d',$data->pointing_id); },
                  'headerOptions'  => ['style' => 'font-weight: normal;'],
              ],
              [
                  'label'          => '顧客名　　　',
                  'value'          => function($data){ return $data->customer->name; },
                  'headerOptions'  => ['style' => 'font-weight: normal;'],
              ],
              [
                  'label'    => '　　ポイント使用',
                  'attribute'=> 'point_consume',
                  'format'   => 'currency',
                  'footer'    => Html::tag('strong', $formatter->asCurrency((int)$model->getPointings(true)->sum('point_consume'))),
                  'headerOptions' => ['class' => 'text-right', 'style' => 'font-weight: normal;'],
                  'contentOptions'=> ['class' => 'text-right'],
                  'footerOptions' => ['class' => 'text-right'],
              ],
              [
                  'label'    => '　　　　ポイント付与',
                  'attribute'=> 'point_given',
                  'format'   => 'currency',
                  'footer'    => Html::tag('strong', $formatter->asCurrency((int)$model->getPointings(true)->sum('point_given'))),
                  'headerOptions' => ['class' => 'text-right', 'style' => 'font-weight: normal;'],
                  'contentOptions'=> ['class' => 'text-right'],
                  'footerOptions' => ['class' => 'text-right'],
              ],
              [
                  'label'    => '（弊社負担分',
                  'format'   => 'currency',
                  'value'    => function($data){ return $data->point_given - $data->point_offset; },
                  'footer'   => Html::tag('strong', $formatter->asCurrency((int)$model->getPointings(true)->sum('point_given') - (int)$model->getPointings(true)->sum('point_offset'))),
                  'headerOptions' => ['class' => 'text-right', 'style' => 'font-weight: normal;'],
                  'contentOptions'=> ['class' => 'text-right'],
                  'footerOptions' => ['class' => 'text-right'],
              ],
              [
                  'label'    => '　貴社ご負担分）',
                  'attribute'=> 'point_offset',
                  'format'   => 'currency',
                  'footer'   => Html::tag('strong', $formatter->asCurrency((int)$model->getPointings(true)->sum('point_offset'))),
                  'headerOptions' => ['class' => 'text-right', 'style' => 'font-weight: normal;'],
                  'contentOptions'=> ['class' => 'text-right'],
                  'footerOptions' => ['class' => 'text-right'],
              ],
          ],
      ]) ?>
      <?php endif ?>

      <!-- #119 【10月度】請求書PDF改修　対応にてコメントアウト
      <?php // if($customer->isAgency() || $model->getCommissions()->exists()): ?>
      <p><strong>■代理店手数料の明細</strong></p>
      <?php 
      // echo \yii\grid\GridView::widget([
      //     'dataProvider' => new \yii\data\ActiveDataProvider([
      //         'query'      => $model->getCommissions(),
      //         'pagination' => false,
      //         'sort'       => false,
      //     ]),
      //     'tableOptions' => ['class'=>'table table-striped table-condensed'],
      //     'emptyText'    => '履歴はありません',
      //     'layout'       => '{items}',
      //     'showFooter'   => true,
      //     'columns'      => [
      //         [
      //             'attribute' => 'purchase.purchase_id',
      //             'value'     => function($data){ return sprintf('%06d',$data->purchase_id); },
      //         ],
      //         [
      //             'attribute' => 'purchase.create_date',
      //             'label'     => '注文日',
      //             'format'    => 'date',
      //         ],
      //         [
      //             'label'     => '販社',
      //             'attribute' => 'company.name',
      //         ],
      //         [
      //             'attribute' => 'customer',
      //             'value'     => function($data){ if($data->customer && $data->customer->grade) return $data->customer->grade->name; },
      //         ],
      //         [
      //             'attribute' => 'customer.code',
      //         ],
      //         [
      //             'attribute' => 'fee',
      //             'format'    => 'currency',
      //             'contentOptions' => ['class'=>'text-right'],
      //             'footer'    => Html::tag('strong', $formatter->asCurrency((int)$model->getCommissions()->sum('fee'))),
      //             'footerOptions' => ['class'=>'text-right'],
      //         ],
      //     ],
      // ]) ?>
      <?php // endif ?>

      <h4>備考</h4>
      <?php
       // echo \yii\grid\GridView::widget([
       //    'dataProvider' => new \yii\data\ActiveDataProvider([
       //        'query'      => $model->getPurchases()->andWhere('0 < CHAR_LENGTH(note)'),
       //        'pagination' => false,
       //        'sort'       => false,
       //    ]),
       //    'showOnEmpty'  => true,
       //    'tableOptions' => ['class'=>'table table-striped table-condensed'],
       //    'emptyText'    => '備考はありません',
       //    'layout'       => '{items}',
       //    'columns'      => [
       //        [
       //            'attribute' => 'purchase_id',
       //            'value'     => function($data){ return sprintf('%06d', $data->purchase_id); },
       //            'headerOptions' => ['style' => 'width:10%; font-weight: normal;'],
       //        ],
       //        [
       //            'attribute' => 'create_date',
       //            'format'    => 'date',
       //            'headerOptions' => ['style' => 'width:15%; font-weight: normal;'],
       //        ],
       //        [
       //            'attribute' => 'note',
       //            'headerOptions' => ['style' => 'width:75%; font-weight: normal;'],
       //        ],
       //    ],
       // ]) ?> -->

       <?php if($model->getPurchases()->exists()): ?>
      <p><strong>■内訳（商品別）</strong></p>
      <?= \yii\grid\GridView::widget([
          'dataProvider' => new \yii\data\ActiveDataProvider([
              'query'      => \common\models\PurchaseItem::find()
                                ->andWhere(['purchase_id' => $model->getPurchases()->select('purchase_id')]),
              'pagination' => false,
              'sort'       => false,
          ]),
          'tableOptions' => ['class'=>'table table-striped table-condensed'],
          'emptyText'    => '明細はありません',
          'showOnEmpty'  => false,
          'layout'       => '{items}',
          'showFooter'   => true,
          'columns'      => [
              [
                  'label'          => '注文日',
                  'format'         => 'date',
                  'value'          => function($data) use ($model) 
                  { 
                    $query = $model->getPurchases()
                                   ->andWhere(['purchase_id' => $data->purchase_id ])
                                   ->select('create_date');
                    return $query->one()->create_date;
                  },
                  'headerOptions'  => ['style' => 'width:12%; font-weight: normal;'],
              ],
              [
                  'attribute'      => 'purchase_id',
                  'value'          => function($data){ return sprintf('%06d',$data->purchase_id); },
                  'headerOptions'  => ['style' => 'width:10%; font-weight: normal;'],
              ],
              [
                  'attribute'      => 'name',
                  'value'          => function($data) use ($model)
                  {
                    $purchase_date = $model->getPurchases()
                                   ->andWhere(['purchase_id' => $data->purchase_id ])
                                   ->select('create_date')->one()->create_date;

                    if(strtotime($purchase_date) >= \common\models\Tax::newDate()) {
                        return $data->tax_rate == \common\models\Tax::findOne(2)->getRate() ? $data->name."※" : $data->name;
                    }
                    return $data->name;

                  },
                  'label'          => '商品名',
                  'format'         => 'html',
                  'headerOptions'  => ['style' => 'width:35%; word-break:keep-all; white-space: normal; font-weight: normal;'],
                  'contentOptions' => ['class' => 'text-left'],
              ],
              [
                  'attribute'      => 'discount_rate',
                  'label'          => '割引(%)',
                  'format'         => 'integer',
                  'headerOptions'  => ['class' => 'text-right', 'style' => 'width:8%; font-weight: normal;'],
                  'contentOptions' => ['class' => 'text-right'],
              ],
              [
                  'attribute'      => 'quantity',
                  'headerOptions'  => ['class' => 'text-right', 'style' => 'width:10%; font-weight: normal;'],
                  'contentOptions' => ['class' => 'text-right info'],
              ],
              [
                  'attribute'      => 'price',
                  'label'          => '単価',
                  'value'          => function($data) use ($formatter)
                  {
                      return $formatter->asCurrency((int)$data->price - (int)$data->discount_amount );
                  },
                  'headerOptions'  => ['class' => 'text-right', 'style' => 'width:9%; font-weight: normal;'],
                  'contentOptions' => ['class' => 'text-right'],
              ],
              [
                  'label'          => '金額',
                  'value'          => function($data) use ($formatter)
                  {
                      return $formatter->asCurrency((int)$data->charge);
                  },
                  'headerOptions'  => ['class' => 'text-right', 'style' => 'width:12%; font-weight: normal;'],
                  'contentOptions' => ['class' => 'text-right'],
              ],
          ],
      ]) ?>
      <?= strtotime($model->target_date) >= \common\models\Tax::newDate() ? "※は軽減税率対象" : "" ?>
      <?php endif ?>

    </div>

</page>
