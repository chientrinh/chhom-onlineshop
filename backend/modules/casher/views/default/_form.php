<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/_form.php $
 * $Id: _form.php 4240 2020-03-18 13:19:11Z mori $
 *
 * $model \common\models\PurchaseForm
 */
use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\helpers\ArrayHelper;
use \common\models\Campaign;

$customer = $model->customer;
$branch   = $this->context->module->branch;

// 追加されている適用書IDの把握, IDの表示と削除リンク設置
$rids = Yii::$app->session->get('recipe-in-casher', []);
$recipes = "";
if(0 != count($rids))
    foreach($rids as $rid)
        $recipes .= Html::tag('span', $rid, ['id' => 'recipe_'.$rid,'class'=>'alert-info','style' => 'font-size:120%']).Html::a('削除',
                                   ['apply',
                                    'target'=> 'recipe_del',
                                    'id'   => $rid,
                                   ],
                                   ['class' => 'btn-xs btn',
                                ])."<br>";


//

/*
$model->compute(false);
$model->validate();
*/
$csscode = "
#purchase-detail th {
    width: 50%;
}
table
{
    table-layout:auto;
}
";
$this->registerCss($csscode);

$out = false;
$over = false;

if($model->branch->isWarehouse() && $model->branch->branch_id == \common\models\Branch::PKEY_ROPPONMATSU)
{
    foreach($model->items as $item)
    {
        if($m = $item->getModel()) {
            if(isset($m->product_id)) {
                $stock_model = \common\models\Stock::find()->where(['product_id' => $m->product_id])->one();
                $stock = \common\models\Stock::getActualQty($m->product_id);
                if(isset($stock_model) && $stock === 0) {
                    $out = true;

                } else if(isset($stock_model) && $item->qty > $stock) {
                    $over = true;
                }
            }
        }
    }
}

$jscode = "
$('input:not([type=checkbox])').change(function(){
    if($(this).attr('id') != $('#barcode').attr('id')) {
        $(this).submit();
    }
    return false;
});
$('select').change(function(){
    $(this).submit();
    return false;
});
$('textarea').change(function(){
    $(this).submit();
    return false;
});
$('#point-consume-btn').click(function(){
    $('#point-consume-ipt').removeAttr('disabled');
    $('#point-consume-ipt').focus();
    return false;
});
$('#point-consume-btn2').click(function(){
    $('#point-consume-ipt').val(".($customer ? $customer->point : 0).");
    $('#point-consume-ipt').removeAttr('disabled');
    $('#point-consume-ipt').form.submit();
    return false;
});
$('#discount-btn').click(function(){
    $('#discount-ipt').removeAttr('disabled');
    $('#discount-ipt').focus();
    return false;
});

// タブフォーカス制御
var list  = $(this).find('*[tabindex]').sort(function(a,b){ return a.tabIndex < b.tabIndex ? -1 : 1; }),
    first = list.first();
    list.last().on('keydown', function(e){
        if( e.keyCode === 9 ) {
            first.focus();
            $('html').scrollTop(0);
            return false;
        }
    });

// ページ読み込み時にはBARCODE欄をまずフォーカス
document.getElementById('barcode').focus();

$('a#pittari-button').on('click', function(){
    // ぴったり！ボタンをクリックしたらお預かりに支払い金額をコピー
    var receive = $('#total_charge')[0].innerText.replace(',','').replace('￥','');
    $('#purchaseform-receive')[0].value = receive;
    $('#purchase-form').submit();
});

var submit_href = $('a#submit-button').attr('href');

$('a#submit-button').on('click', function(){
    var print_name_flg = $('input[name=print_name_flg]').is(':checked');
    $(this).attr('href', submit_href + '?print_name_flg=' + print_name_flg);
});

// 最後に取得したKeyup のkeyCode
var keyUpCode = '';

// keyUpイベント発火したかどうかのフラグ
var keyUpFlg = false;

// ime入力中のフラグ
var imeFlg = false;

// タイマー
var timer;

// submit中フラグ
var set = false;

$('#barcode').keyup(function(e) {
    keyUpCode = e.keyCode;
    keyUpFlg = true;
    if($('#barcode').val().length === 0 || (12 > $('#barcode').val().length && !isNaN($('#barcode').val()))) {
        return false;
    } else {
	    if(imeFlg) {
            if(!set){
                set = true;
                $('#barcode-form').submit();
                return;
            } else {
                return false;
            }
        }
    }
});

$('#barcode').keypress(function(e) {
   imeFlg = false;
});

$('#barcode').keydown(function(e) {
    // 設定していたタイマーのリセット
    clearTimeout(timer);
    // IME入力中フラグのリセット
    imeFlg = false;

    if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
        if($('#barcode').val().length === 0) {
            return false;
        } else {
            if(!set) {
                set = true;
                // $('#barcode-form').submit();
                return;
            } else {
                return false;
            }
        }
    }
    if ((e.which && e.which === 229) || (e.keyCode && e.keyCode === 229)) {
        // IME入力中とみなす
        imeFlg = true;

        // 直前のキーコードをチェックして、入力確定とみなすか判定する
        if(keyUpCode !== 240 && keyUpCode !== 241 && keyUpCode !== 242 && keyUpCode !== 243 && keyUpCode !== 244 && !e.altKey && !e.metaKey && !e.ctrlKey) {
            // キー入力から500ミリ秒後にkeyupイベントの
            // 無い場合は入力確定とする
            timer = setTimeout(function () {
                if (!keyUpFlg || (keyUpFlg && imeFlg)){
                    if(!(12 <= $('#barcode').val().length) && !isNaN($('#barcode').val())){
                        return false;
                    }
                    // submitさせる
                    if(!set) {
                        set = true;
                        $('#barcode-form').submit();
                        return;
                    } else {
                        return false;
                    }
                }
            }, 1000);
            // keyUpFlgのリセット
            keyUpFlg = false;
        }
    }
});

$(window).on('keydown', function(e) {
    if(e.keyCode === 32) {
        // 支払いセレクトボックスと備考欄以外でスペースキーを押したら確定ボタンへフォーカス移動
        if((document.activeElement.tabIndex != $('#purchaseform-payment_id')[0].tabIndex) && (document.activeElement.tabIndex != $('#purchaseform-customer_msg')[0].tabIndex) && !$('a#submit-button').attr('disabled')) {
            $('a#submit-button')[0].click();
            return false;
        }
        return true;
    }
});
";


if($model->point_consume)
    $jscode .= "$('#point-consume-ipt').removeAttr('disabled');";

if($model->discount)
    $jscode .= "$('#discount-ipt').removeAttr('disabled');";

$this->registerJs($jscode);

$tabindex = 1;

?>

<?= $this->render('__nav') ?>

<div class="col-md-12">

    <div class="col-md-3">
        <?php $form0 = \yii\bootstrap\ActiveForm::begin([
            'id'    => 'barcode-form',
            'action' => ['apply','target'=>'barcode'],
            'method' => 'get',
            'fieldConfig' => [
                'enableLabel' => false,
            ],
        ]) ?>

        <?= Html::textInput('barcode', null, [
            'id'         => 'barcode',
            'tabindex'   => $tabindex++,
            #'tabindex'   => $tabindex++,
            'class'      => 'input-lg',
            'placeholder'=> 'BARCODE',
            'size'       => 36,
            'autocomplete' => 'off'
        ]) ?>

        <?php $form0->end() ?>

    </div>

</div>

<div class="col-md-8">

    <?php #$this->render('__items', ['model'=>$model,'tabindex'=>$tabindex]) ?>
    <?= $this->render('__items', ['model'=>$model]) ?>

    <p>
        計 <?= $model->itemCount ?> 点
    <?= Html::a('<i class="glyphicon glyphicon-remove"></i>',['apply','target'=>'reset'],['style'=>'color:#999','title'=>'すべて初期化します']) ?>
    </p>

    <?php if($model->delivery): ?>
        <?= $this->render('__delivery', ['model'=>$model->delivery,'customer'=>$model->customer]) ?>
    <?php endif ?>

    <div style="margin-top:20px">
        <?= $this->render('__customer', ['model'=>$model->customer]) ?>
    </div>


    <div class="jumbotron">
    ただいまの拠点
        <h1><?= $branch ? $branch->name : null ?></h1>
    </div>

</div><!-- col-md-8 -->

<div class="col-md-4">

    <div class="pull-right">
<?php $form = \yii\bootstrap\ActiveForm::begin([
    'id'    => 'purchase-form',
    'action' => ['apply','target'=>'summary'],
    'method' => 'get',
    'fieldConfig' => [
        'enableLabel' => false,
    ],
    'enableClientValidation' => false,
]);

$model->compute(false);
$model->validate();


?>

    <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'well table-condensed table-striped detail-view text-right',
                      'id'    => 'purchase-detail'],
        'attributes' => [
            [
                // 'attribute' => 'campaign',
                'label'     => 'キャンペーン',
                'format'    => 'raw',
                'value'     =>  (! $this->context->campaigns)
                                    ?: $form->field($model, 'campaign_id')
                                            ->dropDownList($this->context->campaigns, ['name' => 'campaign_id']),
                'visible'=> (0 != count($this->context->campaigns))

            ],
            [
                'label'     => '追加適用書',
                'format'    => 'raw',
                'value'     => $recipes,
                'visible'   => (0 != count($rids)) 
            ],
            [
                'attribute' => 'subtotal',
                'format'    => 'raw',
                'value'     => Html::tag('span', Yii::$app->formatter->asCurrency($model->subtotal), ['id' => 'subtotal','style'=>'font-size:150%']),
            ],
            [
                'attribute' => 'tax',
                'format'    => 'raw',
                'value'     => Html::tag('span', Yii::$app->formatter->asCurrency($model->tax), ['id' => 'tax','style'=>'font-size:150%']),
            ],
            [
                'attribute' => 'postage',
                'format'    => 'raw',
                'value'     => Html::tag('span', Yii::$app->formatter->asCurrency($model->postage), ['id' => 'postage']),
                'visible'=> (0 < $model->postage),
            ],
            [
                'attribute' => 'handling',
                'format'    => 'raw',
                'value'     => Html::tag('span', Yii::$app->formatter->asCurrency($model->handling), ['id' => 'handling']),
                'visible'=> (0 < $model->handling),
            ],
            [
                'label'     => Html::a($model->getAttributeLabel('point_consume'), \yii\helpers\Url::current(), ['id'=>'point-consume-btn', 'tabindex' => $tabindex++]),
                'attribute' => 'point_consume',
                'format'    => 'raw',
                    'value' => $form->field($model, 'point_consume')->textInput([
                        'id'       => 'point-consume-ipt',
                        'name'     => 'point_consume',
                        'class'    => 'form-control',
                        'tabindex' => $tabindex++,
                        'disabled' => 'disabled'
                    ]),
                  'visible' => ($model->customer && $model->customer->point),
            ],
            [
                'label'     => Html::a($model->getAttributeLabel('discount'), \yii\helpers\Url::current(), ['id'=>'discount-btn', 'tabindex' => $tabindex++]),
                'format'    => 'raw',
                'value' => $form->field($model, 'discount')->textInput([
                    'id'      => 'discount-ipt',
                    'name'    => 'discount',
                    'class'   => 'form-control',
                    'tabindex' => $tabindex++,
                    'disabled'=> 'disabled',
                    'style'=>'font-size:150%',
                    'tabindex' => $tabindex++,
                ]),
                'labelOptions' => ['class'=>'discount-btn']
            ],
            [
                'label'     => 'お支払い',
                'attribute' => 'total_charge',
                'format'    => 'raw',
                'value'     => Html::tag('span', Yii::$app->formatter->asCurrency($model->total_charge), ['id' => 'total_charge', 'style'=>'font-size:200%']),
            ],
            [
                'attribute' => 'receive',
                'label'     => 'お預かり',
                'format'    => 'raw',
                'value'     => Html::a('ぴったり！', 'javascript:void(0)', [
                                    'id'   => 'pittari-button',
                                    'class' => 'btn-xs btn-success',
                                    'tabindex' => $tabindex++,
                                    'style' => 'margin-top:5px;padding:5px;'
                                ]) . "<br><br>" . $form->field($model, 'receive')->textInput(['name'=>'receive','class'=>'js-zenkaku-to-hankaku form-control','style'=>'font-size:150%', 'tabindex' => $tabindex++]),
//                'visible'   => (\common\models\Payment::PKEY_CASH == $model->payment_id),
            ],
            [
                'attribute' => 'change',
                'format'    => 'raw',
                'value'     => Html::tag('span', Yii::$app->formatter->asCurrency($model->change), ['style'=>'font-size:150%']),
                'visible'   => (\common\models\Payment::PKEY_CASH == $model->payment_id),
            ],
            [
                'attribute' => 'payment_id',
                'format'    => 'raw',
                'value'     => (1 < count($payments))
                             ? $form->field($model, 'payment_id')->dropDownList($payments,['name'=>'payment_id', 'tabindex' => $tabindex++])
                             : ($model->payment ? $model->payment->name : null),
            ],
            [
                'label'     => 'レシートに顧客名を印字',
                'format'    => 'raw',
                'value'     =>
                                // $form->field($model,'payment_id')->checkBox($print_name_flgs,['separator'=>'印字する<br>', 'label' => '印字する']).
                                Html::checkbox('print_name_flg', [1=>'印字する'], ['value' => Yii::$app->request->get('print_name_flg', 1), 'label' => '印字する'])
            ],
        ],
    ])?>

    <div class="text-center">
    <?= Html::a($model->isNewRecord ? '注文を確定する' : '更新する' ,['finish'],[
        'id'   =>'submit-button',
        'class'=>'btn ' . (($model->hasErrors() || $out || $over) ? 'btn-default' : 'btn-danger'),
        'title'=>'',
        'disabled' => ($model->hasErrors() || $out || $over) ? true : false,
        'style' => ($model->hasErrors() || $out || $over) ? 'pointer-events : none;' : '',
        'tabindex' => $tabindex++
    ]) ?>
    <?= $form->errorSummary($model) ?>
    </div>

    <p>
    <?= $form->field($model, 'customer_msg',['template' => "{input}\n{error}"])->textArea(['name'=>'customer_msg','placeholder'=>'備考（社内用メモ）', 'style'=>'border:none;outline:none;', 'tabindex' => $tabindex]); ?>
    </p>

    <?php if($out): ?>
    <p class="alert alert-danger">
        在庫なし商品を含むため、注文いただけません。
    <?php elseif($over): ?>
     <p class="alert alert-danger">
        在庫以上の数はご注文いただけません。
     </p>

    <?php endif ?>

<?php $form->end(); ?>
</div>
</div>
