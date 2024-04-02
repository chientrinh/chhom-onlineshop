<?php 

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/invoice/views/admin/view.php $
 * $Id: view.php 3064 2016-11-02 04:36:21Z mori $
 */

use \yii\helpers\Html;
use \common\models\Invoice;

$formatter = Yii::$app->formatter;

?>

<div class="invoice-default-view">

    <div id="model-status-alert">
    <?php if($model->isPaid()): ?>
        <div class="alert alert-success">
            請求金額は入金されました
        </div>
    <?php elseif($model->isVoid()): ?>
        <div class="alert alert-danger">
            この請求書は無効です
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            入金の確認中です
            <?php if('finance' == $this->context->id): ?>
            <?= Html::a('入金済みにずる',['paid','id'=>$model->invoice_id],['class'=>'btn btn-warning']) ?>
            <?php endif ?>
        </div>
    <?php endif ?>
    </div>

<?php if($model->hasErrors()): ?>

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
  <?= Html::a('再発行する',['update','year'=>$model->year,'month'=>$model->month,'customer_id'=>$model->customer_id],['class'=>'btn btn-warning']) ?>
  </div>

<?php else: /* NO ERROR */ ?>

  <div class="pull-right">
  <?= Html::a(Html::img('@web/img/text-html.png').
  'プレビュー',['print','id'=>$model->invoice_id,'format'=>'html'],['class'=>'btn btn-default']) ?>
  &nbsp;
  <?= Html::a(Html::img('@web/img/application-pdf.png').
  '印刷する',['print','id'=>$model->invoice_id,'format'=>'pdf'],['class'=>'btn btn-default']) ?>
  </div>

<?php endif ?>

    <div id="model-nav" class="text-right">
        <?php if($model->prev): ?>
        <?= Html::a( Html::tag('i','',['class'=>'glyphicon glyphicon-chevron-left']), ['view','id'=>$model->prev->invoice_id], ['class'=>'btn btn-xs btn-default']) ?>
        <?php endif ?>
        <?php if($model->next): ?>
        <?= Html::a( Html::tag('i','',['class'=>'glyphicon glyphicon-chevron-right']), ['view','id'=>$model->next->invoice_id], ['class'=>'btn btn-xs btn-default','title'=>'次']) ?>
        <?php endif ?>
    </div>

    <h1>請求書</h1>

    <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'options' => ['class'=>'table'],
        'attributes' => [
            [
                'attribute' => 'target_date',
                'value'     => $formatter->asDate($model->target_date, 'yyyy-MM'),
            ],
            [
                'attribute' => 'customer_id',
                'label'     => $model->customer->getAttributeLabel('name'),
                'format'    => 'html',
                'value'     => Html::a($model->customer->name,['/customer/view','id'=>$model->customer_id]),
            ],
            [
                'attribute' => 'due_total',
                'format'    => 'html',
                'value'     => Html::tag('div',$formatter->asCurrency($model->due_total), ['class'=>'h3'])
                             . '<div class="row">'
                             . Html::tag('div','内訳',['class'=>'small col-md-4 text-right'])
                             . '<div class="col-md-8">'
                             . \yii\widgets\DetailView::widget([
                                 'model' => $model,
                                 'template' => '<tr><th>{label}</th><td class="text-right">{value}</td></tr>',
                                 'options'  => ['class'=>'table table-condensed'],
                                 'attributes' => [
                                     ['attribute'=>'due_purchase',  'format'=>'currency'],
                                     ['attribute'=>'due_pointing',  'format'=>'currency'],
                                     ['attribute'=>'due_commission','format'=>'currency'],
                                 ],
                             ])
                            . '</div>'
                            . '</div>',
            ],
            [
                'attribute' => 'status',
                'format'    => 'raw',
                'value'     => ($s = $model->getStatus()) ? $s->name : null,
            ],
        ],
    ]) ?>


    <h4>ご注文の明細</h4>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query'      => $model->getPurchases(),
            'pagination' => false,
            'sort'       => false,
        ]),
        'options' => ['class'=>'col-md-12'],
        'tableOptions' => ['class'=>'table table-striped table-condensed'],
        'emptyText' => '履歴はありません',
        'showOnEmpty' => false,
        'layout'      => '{items}',
        'showFooter'  => true,
        'columns'     => [
            [
                'class' => \yii\grid\SerialColumn::className(),
                'footerOptions' => ['class' => 'text-right info'],
            ],
            [
                'attribute'=> 'purchase_id',
                'format'   => 'html',
                'value'    => function($data){ return Html::a($data->purchase_id, ['/purchase/view','id'=>$data->purchase_id]); },
                'footerOptions' => ['class' => 'text-right info'],
            ],
            [
                'attribute'=> 'create_date',
                'format'   => 'date',
                'footer'   => '合計',
                'footerOptions' => ['class' => 'text-right info'],
            ],
            [
                'attribute'=> 'subtotal',
                'format'   => 'currency',
                'contentOptions' => ['class' => 'text-right'],
                'footer'   => $formatter->asCurrency($model->getPurchases()->sum('subtotal')),
                'footerOptions' => ['class' => 'text-right info'],
              ],
              [
                  'attribute'=> 'point_consume',
                  'format'   => 'currency',
                  'value'    => function($data){ return (0 - abs($data->point_consume)); },
                  'contentOptions' => ['class' => 'text-right'],
                  'footer'   => $formatter->asCurrency($model->getPurchases()->sum('point_consume')),
                  'footerOptions' => ['class' => 'text-right info'],
              ],
              [
                  'attribute'=> 'tax',
                  'format'   => 'currency',
                  'contentOptions' => ['class' => 'text-right'],
                  'footer'   => $formatter->asCurrency($model->getPurchases()->sum('tax')),
                  'footerOptions' => ['class' => 'text-right info'],
              ],
              [
                  'attribute'=> 'postage',
                  'format'   => 'currency',
                  'footer'   => $formatter->asCurrency($model->getPurchases()->sum('postage')),
                  'contentOptions' => ['class' => 'text-right'],
                  'footerOptions' => ['class' => 'text-right info'],
              ],
              [
                  'attribute'=> 'handling',
                  'format'   => 'currency',
                  'contentOptions' => ['class' => 'text-right'],
                  'footer'   => $formatter->asCurrency($model->getPurchases()->sum('handling')),
                  'footerOptions' => ['class' => 'text-right info'],
              ],
              [
                  'attribute'=> 'total_charge',
                  'format'   => 'currency',
                  'contentOptions' => ['class' => 'text-right'],
                  'footer'   => Html::tag('strong',$formatter->asCurrency($model->getPurchases()->sum('total_charge'))),
                  'footerOptions' => ['class' => 'text-right info'],
              ],
              [
                  'attribute'=> 'point_given',
                  'format'   => 'integer',
                  'contentOptions' => ['class' => 'text-right'],
                  'footer'   => $formatter->asInteger($model->getPurchases()->sum('point_given')),
                  'footerOptions' => ['class' => 'text-right info'],
              ],
          ],
      ]) ?>
      </div>

      <h4>小売レジの明細</h4>
      <?= \yii\grid\GridView::widget([
          'dataProvider' => new \yii\data\ActiveDataProvider([
              'query'      => $model->getPointings(true),
              'pagination' => false,
              'sort'       => false,
          ]),
          'options'      => ['class'=>'col-md-12'],
          'tableOptions' => ['class'=>'table table-striped table-condensed'],
          'emptyText'    => '履歴はありません',
          'showOnEmpty'  => false,
          'layout'       => '{items}',
          'showFooter'   => true,
          'columns'      => [
              [
                  'class' => \yii\grid\SerialColumn::className(),
                  'contentOptions' => ['class'=>'text-right'],
              ],
              [
                  'attribute' => 'pointing_id',
                  'format'    => 'html',
                  'value'     => function($data){ return Html::a(sprintf('%06d',$data->pointing_id), ['/pointing/view','id'=>$data->pointing_id]); }, 
              ],
              [
                'attribute' => 'create_date',
                'format'    => 'date',
            ],
            [
                'attribute' => 'company.name',
                'value'     => function($data){ if($data->company) return $data->company->name; },
            ],
            [
                'attribute' => 'customer',
                'value'     => function($data){ if($data->customer && $data->customer->grade) return $data->customer->grade->name; },
            ],
            [
                'attribute' => 'customer.code',
            ],
            [
                'attribute' => 'total_charge',
                'format'    => 'currency',
                'contentOptions' => ['class'=>'text-right'],
            ],
            [
                'attribute' => 'point_consume',
                'format'    => 'currency',
                'value'     => function($data){ return 0 - abs($data->point_consume); },
                'contentOptions' => ['class'=>'text-right'],
                'footer'    => Html::tag('strong', $formatter->asCurrency((0 - (int)$model->getPointings(true)->sum('point_consume')))),
                'footerOptions' => ['class'=>'text-right'],
            ],
            [
                'attribute' => 'point_given',
                'format'    => 'integer',
                'contentOptions' => ['class'=>'text-right'],
            ],
            [
                'attribute' => 'point_offset',
                'format'    => 'integer',
                'contentOptions' => ['class'=>'text-right'],
                'footer'    => Html::tag('strong', $formatter->asCurrency((int)$model->getPointings(true)->sum('point_offset'))),
                'footerOptions' => ['class'=>'text-right'],
            ],
          ],
      ]) ?>

      <h4>代理店手数料の明細</h4>
      <?= \yii\grid\GridView::widget([
          'dataProvider' => new \yii\data\ActiveDataProvider([
              'query'      => $model->getCommissions(),
              'pagination' => false,
              'sort'       => false,
          ]),
          'options'      => ['class'=>'col-md-12'],
          'tableOptions' => ['class'=>'table table-striped table-condensed'],
          'emptyText'    => '履歴はありません',
          'showOnEmpty'  => false,
          'layout'       => '{items}',
          'showFooter'   => true,
          'columns'      => [
              [
                  'class' => \yii\grid\SerialColumn::className(),
                  'contentOptions' => ['class'=>'text-right'],
              ],
              [
                  'attribute' => 'purchase.purchase_id',
                  'format'    => 'html',
                  'value'     => function($data){ return Html::a(sprintf('%06d',$data->purchase_id), ['/purchase/view','id'=>$data->purchase_id]); }, 
              ],
              [
                  'attribute' => 'purchase.create_date',
                  'label'     => '注文日',
                  'format'    => 'date',
              ],
              [
                  'attribute' => 'company.name',
              ],
            [
                'attribute' => 'customer',
                'value'     => function($data){ if($data->customer && $data->customer->grade) return $data->customer->grade->name; },
            ],
            [
                'attribute' => 'customer.code',
            ],
              [
                  'attribute' => 'fee',
                  'format'    => 'currency',
                  'footer'    => Html::tag('strong', $formatter->asCurrency((int)$model->getCommissions()->sum('fee'))),
                  'contentOptions' => ['class'=>'text-right'],
                  'footerOptions' => ['class'=>'text-right'],
              ],
          ],
      ]) ?>

      <h4>備考</h4>
      <?= \yii\grid\GridView::widget([
          'dataProvider' => new \yii\data\ActiveDataProvider([
              'query'      => $model->getPurchases()->andWhere('0 < CHAR_LENGTH(note)'),
              'pagination' => false,
              'sort'       => false,
          ]),
          'showOnEmpty'  => true,
          'options'      => ['class'=>'col-md-12'],
          'tableOptions' => ['class'=>'table table-striped table-condensed'],
          'emptyText'    => '備考はありません',
          'layout'       => '{items}',
          'columns'      => [
              [
                  'attribute' => 'create_date',
                  'format'    => 'date',
              ],
              [
                  'attribute' => 'purchase_id',
                  'value'     => function($data){ return sprintf('%06d', $data->purchase_id); },
              ],
              [
                  'attribute' => 'note',
              ],
          ],
      ]) ?>

      <div class="col-md-6">
      <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'options' => ['class'=>'table'],
        'attributes' => [
            [
                'attribute' => 'create_date',
                'format'    => 'datetime',
            ],
            [
                'attribute' => 'update_date',
                'format'    => 'datetime',
            ],
        ],
      ]) ?>
      </div>

      <div class="col-md-6">
      <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'options' => ['class'=>'table'],
        'attributes' => [
            [
                'attribute' => 'created_by',
                'format'    => 'html',
                'value'     => Html::a($model->creator->name,['/staff/view','id'=>$model->created_by]),
            ],
            [
                'attribute' => 'updated_by',
                'format'    => 'html',
                'value'     => Html::a($model->updator->name,['/staff/view','id'=>$model->updated_by]),
            ],
        ],
      ]) ?>
      </div>

      <h4>メール履歴</h4>
      <?= \yii\grid\GridView::widget([
          'dataProvider' => new \yii\data\ActiveDataProvider([
              'query'      => \common\models\MailLog::find()->where(['tbl'=>$model->tableName(), 'pkey'=>$model->invoice_id]),
              'pagination' => false,
              'sort'       => false,
          ]),
          'showOnEmpty'  => true,
          'layout'       => '{items}{pager}',
          'emptyText'    => 'まだありません',
          'tableOptions'   => ['class'=>'table table-condensed table-striped'],
          'summaryOptions' => ['class'=>'small text-right pull-right'],
          'columns' => [
              [
                  'attribute' => 'mailer_id',
                  'format'    => 'html',
                  'value'     => function($data){ return Html::a(sprintf('%06d',$data->mailer_id), ['/mail-log/view', 'id'=>$data->mailer_id]); },
              ],
              [
                  'attribute' => 'date',
                  'format'    => ['date','php:Y-m-d H:i'],
              ],
              [
                  'attribute' => 'subject',
              ],
          ],
      ]); ?>

</div>
