<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/invoice/views/admin/_grid.php $
 * $Id: _grid.php 3848 2018-04-05 09:12:44Z mori $
 */

use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;
use \common\models\Invoice;
use \common\models\InvoiceStatus;
use \common\models\Payment;

$jscode = "
$('.grid-view').on('click','.paid-link',function(e){
   $.ajax({
       url: $(this).attr('href'),
       data: {},
       success: function(data) {
           $(e.target).attr('class','btn btn-xs btn-primary glyphicon glyphicon-ok')
       }
   });
   return false;
});
$('.grid-view').on('click','.active-link',function(e){
   $.ajax({
       url: $(this).attr('href'),
       data: {},
       success: function(data) {
           $(e.target).attr('class','btn btn-xs btn-primary glyphicon glyphicon-ok')
       }
   });
   return false;
});
$('.grid-view').on('click','.send-link',function(e){
   $.ajax({
       url: $(this).attr('href'),
       data: {},
       success: function(data) {
           $(e.target).attr('class','btn btn-xs btn-primary glyphicon glyphicon-ok')
       }
   });
   return false;
});
";
$this->registerJs($jscode);

$labels = ArrayHelper::map(InvoiceStatus::find()->all(),'istatus_id','name');
$dataProvider->pagination->pageSize = 100;

$query = Payment::find()->andWhere([
    'payment_id' => Invoice::find()->select(['payment_id'])->distinct()->column()
]);
$payments = ArrayHelper::map($query->all(),'payment_id','name');
?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'emptyText'    => '結果が得られませんでした。',
    'tableOptions' => ['class'=>'table table-striped table-condensed'],
    'layout'       => '{pager}{items}{pager}{summary}',
    'showFooter'   => true,
    'rowOptions' => function ($data, $key, $index, $grid)
    {
        if(! $data->validate())
            return ['class' => 'danger'];
    },
    'columns'      => [
        [
            'attribute' => 'invoice_id',
            'format'    => 'html',
            'value'     => function($data){ return Html::a($data->invoice_id, ['view','id'=>$data->invoice_id]); },
        ],
/*
        [
            'label'     => '',
            'format'    => 'html',
            'value'     => function($data){
                if($data->isPaid() || $data->isSent())
                    return null;

                $email = $data->customer->email;

                if($email)
                    $text = Html::tag('i','',['class'=>'glyphicon glyphicon-envelope btn btn-xs btn-warning']);
                else
                    $text = Html::tag('i','',['class'=>'glyphicon glyphicon-envelope btn btn-xs btn-default disabled']);

                return Html::a($text, ['sendmail','id'=>$data->invoice_id],[
                        'class'=>'send-link',
                        'title'=>sprintf('メール送信:%s',$email)
                ]);
            },
            'visible'   => ('finance' != $this->context->id),
        ],
 */
        /* [
           'label'     => '',
           'format'    => 'html',
           'value'     => function($data){ return sprintf('%d - %d', $data->isPaid() ,$data->isSent()); },
           'visible'   => ('finance' != $this->context->id),
           ], */
        [
            'attribute' => 'target_date',
            'format'    => 'date',
            'visible'   => ('finance' == $this->context->id),
        ],
        [
            'attribute' => 'customer_id',
            'format'    => 'html',
            'value'     => function($data){ return Html::a(Html::tag('abbr',$data->customer->name,['title'=>$data->customer->isAgency() ? '代理店' : null ]), ['view','id'=>$data->invoice_id],['style'=>'color:black']); },
        ],
        [
            'label'     => '',
            'format'    => 'raw',
            'value'     => function($data){ if($data->due_total) return
                Html::a(Html::img('@web/img/text-html.png'), ['print','id'=>$data->invoice_id,'format'=>'html'],['class'=>'btn btn-xs btn-default'])
              . '&nbsp;'
              . Html::a(Html::img('@web/img/application-pdf.png'), ['print','id'=>$data->invoice_id,'format'=>'pdf'],['class'=>'btn btn-xs btn-default']);
            },
            'headerOptions' => ['class'=>'col-xs-1'],
        ],
        [
            'attribute' => 'status',
            'format'    => 'html',
            'value'     => function($data) use($labels)
            {
                return \yii\helpers\ArrayHelper::getValue($labels, $data->status);
            },
            'filter'    => $labels,
            'headerOptions' => ['class'=>'col-xs-1'],
            'visible'   => ('finance' != $this->context->id),
        ],
        [
            'attribute' => 'status',
            'format'    => 'html',
            'value'     => function($data) use($labels)
            {
                if(InvoiceStatus::PKEY_ACTIVE == $data->status)
                    $btn = Html::a('<i class="glyphicon glyphicon-thumbs-up btn btn-xs btn-warning"></i>',['paid','id'=>$data->invoice_id],['class'=>'paid-link','title'=>'入金済みにする']);
                else
                    $btn = Html::a('<i class="glyphicon glyphicon-share-alt btn btn-xs btn-primary"></i>',['activate','id'=>$data->invoice_id],['class'=>'active-link','title'=>'入金待ちに戻す']);

                return \yii\helpers\ArrayHelper::getValue($labels, $data->status) . $btn;
            },
            'filter'    => $labels,
            'headerOptions' => ['class'=>'col-xs-1'],
            'visible'   => ('finance' == $this->context->id),
        ],
        [
            'attribute' => 'due_total',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right bg-warning'],
        ],
        [
            'attribute' => 'due_purchase',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right text-muted'],
        ],
//        [
//            'attribute' => 'due_commission',
//            'format'    => 'currency',
//            'contentOptions' => ['class'=>'text-right text-muted'],
//        ],
        [
            'attribute' => 'due_pointing',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right text-muted'],
        ],
        [
            'attribute' => 'create_date',
            'format'    => 'date',
            'contentOptions' => ['class'=>'text-muted'],
        ],
        [
            'attribute' => 'update_date',
            'format'    => 'date',
        ],
        ['class' => 'yii\grid\DataColumn' ,
            'attribute' => '代理店',
            //'format'    => 'raw',
            'value'     => function($data)
            {
                return true == $data->customer->isAgency() ? '代理店' : '個人';
            },
            'filter' => Html::activeDropDownList($searchModel, "is_agency", [
               '' => '',
               1 => '個人',
               2 => '代理店',
            ],['class'=>'form-control']),
        ],
        [
            'attribute' => 'payment_id',
            'format'    => 'text',
            'value'     => function($data){ if($p = $data->payment) return $p->name; },
            'filter'    => $payments,
        ],
        [
            'attribute' => 'created_by',
            'format'    => 'html',
            'value'     => function($data){ return Html::a($data->creator->name01, ['/staff/view','id'=>$data->created_by],['class'=>'text-muted']); },
        ],
        [
            'attribute' => 'updated_by',
            'format'    => 'html',
            'value'     => function($data){ return Html::a($data->updator->name01, ['/staff/view','id'=>$data->updated_by],['class'=>'text-muted']); },
        ],
        // 'company_id',
    ],
]) ?>
