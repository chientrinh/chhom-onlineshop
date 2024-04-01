<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/cart/views/default/_cart.php $
 * $Id: _cart.php 4184 2019-09-18 06:09:01Z mori $
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use common\models\Company;
use common\models\Payment;
use common\models\CustomerGrade;

$cart_idx = $key;

$formatter = new \yii\i18n\Formatter();

$out = [];
foreach($model->items as $item)
{
    if(($m = $item->model) && $m instanceof \yii\db\ActiveRecord)
        if($m->hasAttribute('in_stock') && ! $m->in_stock)
            $out[] = $item->name;
}

$model->purchase->direct_code = $model->delivery->code;
$direct_customers = [];
if(Yii::$app->user->identity) {
    $direct_customers = \common\models\CustomerAddrbook::find()
            ->andWhere(['customer_id' => Yii::$app->user->identity->customer_id])
            ->andWhere(['not', ['code' => null]])
            ->andWhere(['>', 'LENGTH(email)', 0])
            ->andWhere(['>', 'LENGTH(code)', 0])
            ->select(['id','code','name01','name02'])
            ->all();
    $direct_customers = \yii\helpers\ArrayHelper::map($direct_customers, 'code', function($element) {
        return $element['code'].":".$element['name01'].$element['name02'];
    });
}

$payment_url = Url::to(['update', 'target' => 'payment', 'cart_idx' => $cart_idx]);

$csscode = "
#loading-view {
 /* 領域の位置やサイズに関する設定 */
 width: 100%;
 height: 100%;
 z-index: 9999;
 position: fixed;
 top: 0;
 left: 0;
 /* 背景関連の設定 */
 background-color: #000000;
 filter: alpha(opacity=85);
 -moz-opacity: 0.85;
 -khtml-opacity: 0.85;
 opacity: 0.85;
 background-image: url(/img/loading.gif);
 background-position: center center;
 background-repeat: no-repeat;
 background-attachment: fixed;
}
#text {
  width: 100%;
  height: 100%;
  color:#FFFFFF;
  font-size:14px;
  font-weight:bold;
  padding-top : 100px;
  /* Firefox */
  display: -moz-box;
  -moz-box-pack: center;
  -moz-box-align: center;
  /* Safari and Chrome */
  display: -webkit-box;
  -webkit-box-pack: center;
  -webkit-box-align: center;
  /* W3C */
  display: box;
  box-pack: center;
  box-align: center;
}
";

$this->registerCss($csscode);

$jscode = "
chk1 = $('#out-of-stock-accept');
chk2 = $('#i-am-adult');
if(
  ((0 < chk1.length) && ! chk1.is(':checked')) ||
  ((0 < chk2.length) && !(20 <= chk2.val()) )
)
    $('#cart-finish').attr('disabled',true);

$('#out-of-stock-accept').click(function()
{
  chk2 = $('#i-am-adult');

  if(! $(this).is(':checked'))
    $('#cart-finish').attr('disabled',true);

  else if(0 == chk2.length || (20 <= chk2.val()))
      $('#cart-finish').removeAttr('disabled');
});

$('#i-am-adult').change(function()
{
  chk1 = $('#out-of-stock-accept');

  if(! (20 <= $(this).val()))
    $('#cart-finish').attr('disabled',true);

  else if(0 == chk1.length || chk1.is(':checked'))
    $('#cart-finish').removeAttr('disabled');
});

$('[name=payment]').change(function()
{
    payment = $('[name=payment]:checked')[0].value;
    console.log(payment);
    $('#cart-finish').data('params', {'payment' : payment});
        var data = ('params', {'payment' : payment});
    $.ajax( {
        type: 'POST',
        url: '".$payment_url."',
        data: data,
        dataType: 'json',
        cache : false,
        timeout: 30000,
        beforeSend: function ( jqXHR, settings ) {
            console.log(jqXHR);
            loadingView(true);
            $('a').attr('disabled', true);
        },
        success: function( data ) {
            console.log(data);
        },
        error: function( data ) {
            console.log('error');
            console.log(data);
        }
    } );

});

function loadingView(flag) {
  $('#loading-view').remove();
  if(!flag) return;
  $('<div><div id=\'loading-view\'><div id=\'text\'>しばらくお待ち下さい...</div></div></div>').appendTo('body');
}


$('#cart-finish').click(function()
{
  $(this).attr('disabled',true);
});

$('#input-agent-toggle').click(function(){
    $(this).hide();
    $('#input-agent-still').hide();
    $('#input-agent-dynamic').show();
    $('#input-agent-cancel-toggle').show();
    return false;
});
$('#input-agent-cancel-toggle').click(function(){
    $(this).hide();
    $('#input-agent-still').show();
    $('#input-agent-dynamic').hide();
    $('#input-agent-toggle').show();
    return false;
});

";
$this->registerJs($jscode);

?>

<div class="cart-<?= $key ?>">

    <div class="col-md-8">
        <div class="row">

            <?php if($out): ?>
            <div class="col-md-12" style="margin-bottom:10px">
                <div class="well-sm alert-danger" id="out-of-stock-accept">
                申し訳ありませんが以下の商品はただいま在庫がありません。<br>
                カートから削除してください。<br><br>
                <?= Html::ul($out,['style'=>'list-style-type: square','class'=>'strong']) ?>
            </div>
            </div>
            <?php endif ?>



            <div class="col-md-12">
                <?php echo $this->render('_items', ['cart_idx' => $key, 'model' => $model, 'editable'=>true]) ?>
            </div>

            <p class="text-left">
                <?= Html::a('お買い物を続ける','/',['class'=>'btn btn-warning']) ?>
            </p>


        </div>
    </div>

    <div class="col-md-4">
        <div class="Detail-Total">
            <div class="inner">

                <?= \yii\widgets\DetailView::widget([
                    'model' => $model->purchase,
                    'template' => '<tr><th>{label}</th><td class="text-right">{value}</td></tr>',
                    'attributes' => [
                        [
                            'attribute' => 'taxedSubtotal',
                            'format'    => 'currency',
                            'label'     => '商品計（税込）',
                        ],
                        [
                            'attribute' => 'discount',
                            'format'    => 'raw',
                            'value'     => Html::tag('span', $formatter->asCurrency(abs($model->purchase->discount))),
                        ],
                        [
                            'attribute'=> 'total_charge',
                            'format'   => 'raw',
                            'value'    => Html::tag('span', $formatter->asCurrency($model->purchase->total_charge),['class'=>'Total']),
                        ],
                    ],
                ]);?>

                <?php if($model->hasErrors()):
                    if(isset($model->purchase->errors["items"]) && in_array("商品がありません",$model->purchase->errors["items"])) {
//                        echo '<p class="alert alert-info">商品なし</p>';
                        for ($i=0; $i < count($model->purchase->errors["items"]); $i++) {
                            if($model->purchase->errors["items"][$i]!="商品がありません"){
                                echo '<p class="alert alert-danger">'.$model->purchase->errors["items"][$i].'</p>';   
                            }
                        }

                    } else {
                        echo '<small>'.
                            Html::errorSummary($model,['class'=>'error-summary']);
                        echo '</small>';
                    }
                ?>

                <?php else: 
                    // カートにエラーが検出されず、
                    // カート内に商品が1種類以上入っていればボタン表示
                    if(0 < count($model->items)) {
                        echo '<p class="text-center">
                            <span class="detail-view-btn">';
                    ?>
                     <?=           Html::a("注文を確定する", ['finish', 'cart_idx' => $cart_idx], [
                                    'id'    => 'cart-finish',
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'method' => 'post',
                                        'params' => ['payment' => $model->purchase->payment_id],
                                    ],
                                ]); ?>
                    <?php
                        echo'    </span>
                        </p>';
                    }
                    
                endif ?>

<?php if(!$model->purchase->agent_id && Yii::$app->user->id != $model->customer->id): ?>
                    <hr>
                    <div class="text-danger">
                    <h5>ご注文者</h5>
                    <p id="purchase-customer-name"><?= $model->customer->name ?></p>
                    </div>
                <?php endif ?>
                <?php if((0 < Yii::$app->user->id) && ((Yii::$app->user->id == $model->customer->id) || ($model->purchase->agent_id && Yii::$app->user->id == $model->purchase->agent_id))): ?>
                    <?php if($customer->grade_id >= CustomerGrade::PKEY_TA && $support_entry): ?>
                <hr>
                    <h5>
                    サポート申込
                        <?php if($model->purchase->agent_id): ?>
                            <?= Html::a("変更", ['update', 'target'=>'agent', 'cart_idx'=> $cart_idx],['class'=>'btn btn-default','id'=>'input-agent-toggle']) ?>
                        <?php endif ?>
                    </h5>
                    <p>
                        <span id="input-agent-still">
                        <?= $model->purchase->agent_id ? $model->delivery->name : "相手先を選択した後に、「設定」ボタンをクリックすると、「ご連絡先」が表示されます。相手様を確認後、「注文を確定する」クリックにて、サポート申込が適用されます。" ?>
                        </span>
                       <?php if($model->purchase->agent_id): ?>
                        <div id="input-agent-dynamic" style="display:none">
                       <?php else: ?>
                        <div id="input-agent-dynamic">
                       <?php endif ?>
                        <?php $form = \yii\bootstrap\ActiveForm::begin([
                            'id'     => 'form-update-agent',
                            'method' => 'post',
                            'action' => ['update','target'=>'agent','cart_idx'=>$cart_idx],
                            'fieldConfig'=> ['template'=>'{input}{error}'],
                            'enableClientValidation' => false,
                        ]) ?>
                        <?= $form->field($model->purchase, 'direct_code')->dropDownList($direct_customers,
                                ['prompt' => '相手先を選択してください'],
                                ['options' => [$model->purchase->direct_code => ['Selected'=> true]]]
                            ) ?>
                       <?php if(!$model->purchase->agent_id): ?>
                        <?= Html::submitButton('設定',['name'=>'submit_agent','class'=>'btn btn-sm btn-success']) ?>
                       <?php else: ?>
                        <?= Html::submitButton('更新',['name'=>'submit_agent','class'=>'btn btn-xs btn-primary']) ?>
                        <?= $model->purchase->agent_id ? Html::a("削除", ['update', 'target'=>'agent-del', 'cart_idx'=> $cart_idx],['class'=>'btn btn-xs btn-danger']) : "" ?>
                        <?= Html::a("閉じる", ['update', 'target'=>'agent', 'cart_idx'=> $cart_idx],['class'=>'btn btn-xs btn-default','id'=>'input-agent-cancel-toggle']) ?>
                       <?php endif ?>
                        <?php $form::end() ?>
                        </div>
                        <br>
                    </p>
                     <?php endif ?>
                <?php endif ?>

                <hr>
                <?php if(Yii::$app->user->isGuest): ?>  
                    <hr>
                    <h5>ご連絡先<?= Html::a("変更",['/cart/guest/signup'],['class'=>!$customer->email || $customer->hasErrors('email')?'btn btn-danger':'btn btn-default']) ?>
                    </h5>
                    <p>
                        <?= $model->delivery->email ?>
                        <br>
                        <p class="help-block"><?= $model->purchase->getAttributeHint('email') ?></p>
                <p>〒<?= $model->delivery->zip ?><br>
                    <?= $model->delivery->addr ?><br>
                    <?= $model->delivery->name ?> 様
                </p>

                    </p>
                <?php else: ?>

                <h5>ご連絡先</h5>
                <p><?= $model->delivery->email ?><br>
                </p>
                <p>〒<?= $model->delivery->zip ?><br>
                    <?= $model->delivery->addr ?><br>
                    <?= $model->delivery->name ?> 様
                </p>
                <?php endif ?>

                <?php (Payment::PKEY_DROP_SHIPPING === $model->purchase->payment_id) ? $class='alert-info' : $class = '' ?>
                <h5>ご購入方法
                </h5>
                <?php if($model->purchase->agent_id) {
                    echo '<p id="purchase-paynent-name" name="payment" class="'.$class.'">指定なし</p>';
                } else if(in_array(Payment::PKEY_DIRECT_DEBIT, $model->payments)) {
                    foreach($model->payments as $payment_id)
                    {

                        $payment = Payment::findOne($payment_id);
                        $options = [
                            'id'     => 'purchase-payment-name',
                            'label'  => $payment->name,
                            'value'  => $payment_id,
                            'uncheck'=> null,
                            'checked'=> null,
                        ];

                        $checked = (($p = $model->purchase->payment_id) && ($p == $payment_id)) ? true : false;
                        echo '<div>'.Html::radio('payment', $checked, $options).'</div>';
                    }

                } else { ?>
                <p id="purchase-paynent-name" class="<?= $class ?>"><?= $model->purchase->payment->name ?></p>
             <?php } ?>

                    <hr>

                    <?php if(Yii::$app->session->get('live_notes_companion')):?>
                    <h5>
                        チケット予約詳細
                            <?= Html::a("同行者情報の変更", ['update', 'target'=>'companion', 'cart_idx'=> $cart_idx],['class'=>'btn btn-default']) ?>
                    </h5>
                    <p>
                        <span id="input-event-companion-still">
                        <?= $model->purchase->note ?><br>
                        </span>

                        </div>
                    </p>
                <?php else: ?>

                    <h5>チケット予約詳細
                    </h5>
                        <p><?= $model->purchase->note ?><br>
                        </p>
                        <?php endif ?>

                    <hr>
                    <?php $form = \yii\bootstrap\ActiveForm::begin([
                        'action' => ['update','cart_idx'=>$cart_idx,'target'=>'msg'],
                        'method' => 'post',
                        'fieldConfig' => [
                            'template' => '{input}{error}',
                        ],
                    ]) ?>


                    <h5>
                        <?= $model->purchase->getAttributeLabel('customer_msg') ?>　<span style="color:red">（入力後、必ず「保存」ボタンをクリックしてください）</span>
                        <?= Html::submitButton('保存',['name' => 'submit_msg','class'=>'btn btn-default']) ?>
                    </h5>
                    <p>
                        <?= $form->field($model->purchase,'customer_msg')->textArea(['id'=>'customer_msg','name'=>'customer_msg','placeholder'=>$model->purchase->getAttributeHint('customer_msg'),'style'=>'outline:none;']) ?>
                        <?php $form->end() ?>
                    </p>

            </div>
        </div>

    </div><!-- col-md-4 -->

</div>
