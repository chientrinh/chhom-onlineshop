<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/__items.php $
 * $Id: __items.php 3934 2018-06-20 03:57:25Z mori $
 *
 * @param $model    PurchaseForm
 * @param $tabindex integer
 */

use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;

// アイテムリストの最終Index
$last_idx = count($model->items)-1;

$jscode = "
$('.qty-txt').click(function(){
    $(this).hide();
    $('.input-group').hide();
    $('#ipt-' + $(this).attr('id')).show();
    return false;
});
$('.reduce-per-txt').click(function(){
    $('.input-group').hide();
    $('#reduce-grp-' + $(this).attr('id')).show();
    return false;
});
$('.reduce-yen-txt').click(function(){
    $('.input-group').hide();
    $('#reduce-grp-' + $(this).attr('id')).show();
    return false;
});
$('.btn.close').on('click',function(e)
{
    $.ajax({
         url:  $(this).attr('href'),
         type: 'get',
         success: function (data) {
             data = JSON.parse(data);
             if(data.hasOwnProperty('subtotal'    )){ $('#subtotal'    ).html(data.subtotal);     }
             if(data.hasOwnProperty('tax'         )){ $('#tax'         ).html(data.tax);          }
             if(data.hasOwnProperty('postage'     )){ $('#postage'     ).html(data.postage);      }
             if(data.hasOwnProperty('handling'    )){ $('#handling'    ).html(data.handling);     }
             if(data.hasOwnProperty('total_charge')){ $('#total_charge').html(data.total_charge); }
             if(data.hasOwnProperty('item'))
             {
                 var item = data.item;
                 $('#qty-'+item.seq).html(item.quantity);
                 $('#ipt-qty-'+item.seq).val(item.quantity);
                 $('#itm-'+item.seq+'-charge').html(item.charge);
             }
             if(data.hasOwnProperty('widget'))
             {
               $('.grid-view').html(data.widget); // refresh entire table
               $('.grid-view').find('a').remove(); // don't show <a>
             }
         },
         error: function(response) {
              alert(response);
         }
    });

    return false;
});
";

$this->registerJs($jscode);
?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => new \yii\data\ArrayDataProvider([
        'allModels'  => array_reverse($model->items),
        'pagination' => false,
    ]),
    'layout'       => '{items}',
    'emptyText'    => '商品はありません',
    'tableOptions' => ['class' => 'table-striped grid-view table-condensed','style'=>'width:100%'],
    'rowOptions'   => ['style' => 'vertical-align:text-top'],
    'showFooter'   => $model->point_given,
    'columns'      => [
        [
            'attribute' => 'company',
            'format'    => 'html',
            'value'     => function($data,$key,$idx,$col){ return strtoupper(ArrayHelper::getValue($data,'company.key')); },
            'visible'   => (! $model->company),
        ],
        [
            'attribute' => 'code',
            'format'    => 'html',
        ],
        [
            'attribute' => 'name',
            'format'    => 'html',
            'value'     => function($data,$key,$idx,$col)use($model, $last_idx)
            {
                $out = null;
                $m = $data->model;
                if(isset($m->product_id)) {
                    $stock_model = \common\models\Stock::find()->where(['product_id' => $m->product_id])->one();
                    $stock = \common\models\Stock::getActualQty($m->product_id);
                    if($model->branch->isWarehouse() && $model->branch->branch_id == \common\models\Branch::PKEY_ROPPONMATSU) {
                        if(isset($stock_model) && $stock === 0) {
                            $out = Html::tag('span','在庫なし',['class'=>'not-set alert alert-danger']);
                        } else if(isset($stock_model) && ($data->qty > $stock)) {
                            $count = abs($data->qty - $stock);
                            $out = Html::tag('span','在庫が '.$count.'不足',['class'=>'not-set alert alert-danger']);
                        } else {
                            $out = null;
                        }
                    }
                }
                $add_label = null;
                if($data->campaign_id && isset($model->campaign->campaign_name))
                    $add_label = "<br>".Html::tag('span',$model->campaign->campaign_name,['class'=>'btn-xs alert-success pull-right']);   
//                if($data->is_wholesale)
//                    $add_label = "<br>".Html::tag('span','卸売',['class'=>'btn-xs alert-info pull-right']);

                return Html::a('', ['apply','target'=>'quantity','seq'=>$last_idx-$idx,'vol'=>(0 - $data->quantity)],['title'=>"削除します",'class'=>'close glyphicon glyphicon-remove'])
                           . nl2br($data->name)
                           . $out
                           . $add_label;
            },
            'headerOptions' =>['class'=>'col-md-4'],
        ],
        [
            'attribute' => 'price',
            'format'    => 'raw',
            'value'     => function($data,$key) use($model, $last_idx)
            {
                return $this->render('_reduce',['model'=>$data,'key'=>$last_idx-$key, 'purchase' => $model]);
            },
            'contentOptions' =>['class'=>'text-right'],
        ],
/*
        [
            'attribute' => 'unitPrice',
            'format'    => 'raw',
            'value'     => function($data)
            {
                return $data->getUnitPrice();
            }
        ],
        [
            'attribute' => 'unitTax',
            'format'    => 'raw',
            'value'     => function($data)
            {
                return $data->getUnitTax();
            }
        ],

        [
            'attribute' => 'pointConsume',
            'format'    => 'raw',
            'value'     => function($data)
            {
                return $data->getPointConsume();
            }
        ],
*/
        [
            'attribute' => 'qty',
            'format'    => 'raw',
            #'value'    => function($data,$key,$idx,$col)use($tabindex, $last_idx)
            'value'    => function($data,$key,$idx,$col)use($last_idx)
            {
                #return $this->render('_ajax', ['model'=>$data,'key'=>$last_idx-$key,'idx'=>$last_idx-$idx,'tabindex'=>$tabindex]);
                return $this->render('_ajax', ['model'=>$data,'key'=>$last_idx-$key,'idx'=>$last_idx-$idx]);
            },
            'headerOptions' =>['class'=>'col-sm-2 text-center'],
        ],
        [
            'attribute' => 'charge',
            'format'    => 'html',
            'value'     => function($data)use($model)
            {
                $tag = Html::tag('p',Yii::$app->formatter->asCurrency($data->charge));
                $point_rate = $data->getPointRate()."%";
                return ( $data->campaign_id && isset($model->campaign->campaign_type) && $model->campaign->campaign_type == \common\models\Campaign::DISCOUNT && !$data->getPointRate()) ? $tag : $tag
                     . Html::tag('span',($data->getPointTotal() ? $point_rate.' '.number_format($data->getPointTotal()) . 'pt' : ''),['class'=>'text-muted']);
            },
            'footer'    => '計' . number_format($model->point_given) . 'pt',
            'contentOptions' => function ($model, $key, $index, $column)use($last_idx){$key = $last_idx-$key; return ['id' => "itm-{$key}-charge", 'class'=>'text-right']; },
            'headerOptions' =>['class'=>'col-md-2'],
            'footerOptions' =>['class'=>'text-right'],
        ],
    ],
]) ?>

