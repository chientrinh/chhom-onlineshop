<?php

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/purchase/view.php $
 * $Id: view.php 4159 2019-05-10 04:05:24Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Purchase
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\ChangeLog;
use common\models\Payment;
use common\models\PurchaseStatus;

$this->params['breadcrumbs'][] = ['label' => '売上', 'url' => 'index'];
$this->params['breadcrumbs'][] = ['label' => $model->purchase_id];

$jscode = "
$('#toggle-btn-1').click(function(){
         $('#sub-menu-1').toggle();
 	return true;
});
$('#toggle-btn-2').click(function(){
         $('#sub-menu-2').toggle();
 	return true;
});
$('#toggle-btn-3').click(function(){
         $('#sub-menu-3').toggle();
 	return true;
});
$('#toggle-btn-4').click(function(){
         $('#sub-menu-4').toggle();
 	return true;
});
$('#toggle-btn-5').click(function(){
         $('#sub-menu-5').toggle();
 	return true;
});
$('#toggle-btn-6').click(function(){
         $('#sub-menu-6').toggle();
 	return true;
});
$('#toggle-btn-7').click(function(){
         $('#sub-menu-7').toggle();
 	return true;
});
$('#toggle-btn-8').click(function(){
         $('#sub-menu-8').toggle();
        return true;
});
";
$this->registerJs($jscode);

//$statusList = PurchaseStatus::find()->where(['<=','status_id',PurchaseStatus::PKEY_DONE])->andFilterWhere(['>=','status_id',$model->status])->all();
$statusList = PurchaseStatus::find()->all();
$statusList = ArrayHelper::map($statusList,'status_id','name');

$branch = Yii::$app->controller->id;
if ($branch == 'trose')
{
    $query = Payment::find()->where(['payment_id'=>[Payment::PKEY_POSTAL_COD,
                                                    Payment::PKEY_PARCEL_COD,]]);
}
else
{
    $query = Payment::find()->where(['payment_id'=>[Payment::PKEY_CASH,
                                                Payment::PKEY_YAMATO_COD,
                                                Payment::PKEY_BANK_TRANSFER,]]);
}
$payments = ArrayHelper::map($query->all(), 'payment_id','name');

// 適用書の紐付けを確認して、取消ボタンクリック時のダイアログテキストを切り替える
$cancel_alert_msg = "本当にこの伝票を取り消してよろしいでしょうか？";
$recipes = ArrayHelper::getColumn($model->purchaseRecipe, 'recipe_id');
if(isset($recipes) && count($recipes) > 0)
    $cancel_alert_msg = "この伝票には適用書ID:".implode(",",$recipes)."が追加されていますが、本当に取り消してよろしいでしょうか？";


?>

<div class="purchase-view">

    <h1><?= $model->getAttributeLabel('purchase_id') ?> : <?= Html::encode($model->purchase_id) ?></h1>

    <p class="pull-right">
    <?php if('casher' == $this->context->module->id): ?>
        <?= Html::a('修正', ['update', 'id' => $model->purchase_id], ['class' => 'btn btn-primary']) ?>
    <?php endif ?>
        <?= Html::a('返品', ['/purchase/refund', 'id' => $model->purchase_id], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('取消', ['/purchase/cancel', 'id' => $model->purchase_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => $cancel_alert_msg,
                'method'  => 'post',
            ],
        ]) ?>
    </p>

    <p>
        <div class="btn-group">
            <?= Html::a('納品書',
                        ['print', 'id' => $model->purchase_id, 'format'=>'pdf'],
                        ['class' => 'btn btn-default']) ?>
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li role="presentation"><?= Html::a('納品書',['print', 'id' => $model->purchase_id, 'format'=>'pdf'],['role'=>"menuitem"]) ?></li>
                <li role="presentation"><?= Html::a('納品書（健康相談）',['print', 'id' => $model->purchase_id, 'format'=>'pdf', 'target' => 'sodan'],['role'=>"menuitem"]) ?></li>
                <li role="presentation"><?= Html::a('納品書のみ',['print', 'id' => $model->purchase_id, 'format'=>'pdf', 'target'=>'delivery'],['role'=>"menuitem"]) ?></li>
                <li role="presentation"><?= Html::a('仕訳票のみ',['print', 'id' => $model->purchase_id, 'format'=>'pdf', 'target'=>'picking'],['role'=>"menuitem"]) ?></li>
                <li role="presentation"><?= Html::a('チェーンストア統一伝票',['/purchase/print', 'id' => $model->purchase_id, 'format'=>'pdf', 'target'=>'chainstore'],['role'=>"menuitem"]) ?></li>
            </ul>
        </div>


        <?= Html::a('レシート', ['receipt', 'id' => $model->purchase_id], [
            'class' => 'btn btn-default',
            'style' => $model->isExpired() ? 'background:lightgray' : null ,
        ]) ?>

        <?php if(in_array($model->shipped, [0, 1])): ?>
        <?= Html::a('ヤマト便CSV',
                    ['print-csv', 'id' => $model->purchase_id],
                    ['class' => 'btn btn-default',
                     'title' => $model->isPreparing() ? '出荷済のためCSVは不要です' : 'ヤマト便のためにCSVを出力します',
                     'style' => $model->isPreparing() ? 'background:lightgray' : null ,
                    ]) ?>
        <?= Html::a("ゆうプリ用CSV",
                    ['print-csv-for-yu-print', 'id' => $model->purchase_id],
                    ['class' => 'btn btn-default',
                     'title' => $model->isPreparing() ? '出荷済のためCSVは不要です' : 'ゆうパックプリントのためにCSVを出力します',
                     'style' => $model->isPreparing() ? 'background:lightgray' : null ,
                    ]) ?>
        <?php endif ?>
        <?php if($model->getItemsToDrop()): ?>
        <?php endif ?>

        <div class="btn-group">
            <?= Html::a('荷札',
                        ['print-label','id' => $model->purchase_id, 'target'=>'sticker'],
                        ['class' => 'btn btn-default',
                         'title' => '大口顧客向けにバーコードシールを出力します',
                         'style' => $model->isExpired() ? 'background:lightgray' : null ,
                        ]) ?>
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li role="presentation"><?= Html::a('荷札',['print-label', 'id' => $model->purchase_id, 'format'=>'pdf', 'target'=>'sticker'],['title'=>'大口顧客向けにバーコードシールを出力します','role'=>"menuitem"]) ?></li>
                <li role="presentation"><?= Html::a('値札',['print-label', 'id' => $model->purchase_id, 'format'=>'pdf', 'target'=>'price'],['title'=>'店頭に陳列するための値札を出力します','role'=>"menuitem"]) ?></li>
                <?php if(!Yii::$app->user->identity->hasRole(["tenant"])) { ?>
                <li role="presentation">
                    <?= Html::a('レメディーラベル',
                                ['print-remedy-label', 'id' => $model->purchase_id],
                                ['title' => '滴下レメディーのラベルを出力します',
                                 'style' => $model->isExpired() ? 'background:lightgray' : null ,
                                 'role'  => "menuitem"]) ?>
                </li>
                <?php } ?>
            </ul>
        </div>
</div>
    <p>
    </p>

    <?php if($model->isExpired()): ?>
        <p class="alert alert-danger">
            この伝票は無効です。
        </p>
    <?php endif ?>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query'      => $model->getItems(),
            'pagination' => false,
        ]),
        'layout'  => '{items}',
        'columns' => [
            'code',
            [
                'attribute' => 'name',
                'format'    => 'html',
                'value'     => function($data){
                    if($data->product_id)
                        return Html::a($data->name,['/product/view','id'=>$data->product_id]);
                    if($data->remedy_id)
                        return Html::a($data->name,['/remedy/view','id'=>$data->remedy_id]);
                    return $data->name;
                }
            ],
            'is_wholesale',
            [
                'attribute' => 'price',
                'format'    => 'currency',
                'contentOptions' => ['class'=>'text-right'],
            ],
            [
                'attribute' => 'point_rate',
                'contentOptions' => ['class'=>'text-right'],
                'value'     => function($data){ return intval($data->point_rate) . '%'; },
            ],
            [
                'attribute'=>'discount_rate',
                'value'     => function($data){ return intval($data->discountRate) . '%'; },
                'contentOptions' => ['class'=>'text-right'],
            ],
            [
                'attribute' => 'quantity',
                'format'    => 'integer',
                'contentOptions' => ['class'=>'text-center'],
            ],
            [
                'attribute' => 'charge',
                'format'    => 'currency',
                'contentOptions' => ['class'=>'text-right'],
            ],
            [
                'attribute' => 'pointTotal',
                'format'    => 'integer',
                'contentOptions' => ['class'=>'text-right'],
            ],
        ],
    ]) ?>

    <strong><?= $model->getAttributeLabel('delivery') ?></strong>
    <?php if($model->delivery): $deli = $model->delivery;$purchase = $model; ?>
        <?= DetailView::widget([
            'model'   => $deli,
            'options' => ['class' => 'table table-condensed table-bordered detail-view text-right'],
            'attributes' => [
                [
                    'attribute' => 'addr',
                    'value'     => sprintf('〒%s %s', $deli->zip, $deli->addr),
                ],
                [
                    'attribute' => 'name',
                    'format'    => 'raw',
                    'value'     => $deli->name
                ],
                'tel',
                [
                    'attribute' => 'email',
                    'format'    => 'raw',
                    'value'     => $model->email .'&nbsp;'. Html::a('送信',['/purchase/sendmail','id'=>$model->purchase_id],['class'=>'btn btn-xs btn-success','title'=>'メールを書きます'])
                ],
                [
                    'attribute' => 'expect_date',
                    'value'     => $model->delivery->datetimeString,
                ],
                [
                    'attribute' => 'gift',
                    'value'     => $model->isGift() ? '非表示' : '表示',
                ],
                [
                    'label'     => '代理注文者',
                    'value'     => $purchase->agent_id ? "会員ID:".$purchase->agent_id." ".\common\models\Customer::findOne(['customer_id' => $purchase->agent_id])->name : null,
                    'visible'   => $purchase->agent_id ? true : false,
                ],

            ],
        ]) ?>
    <?php else: ?>
        (なし)
    <?php endif ?>

    <div class="well">
        <p id="customer_msg">
            <strong><?= $model->getAttributeLabel('customer_msg') ?></strong>
            <?= Html::encode(strlen($model->customer_msg) ? $model->customer_msg : "(なし)") ?>
            <?= Html::button('変更',['id'=>'toggle-btn-7','class'=>'btn btn-xs btn-default']) ?>
        </p>
        <p id="note">
            <strong><?= $model->getAttributeLabel('note') ?></strong>
            <?= Html::encode(strlen($model->note) ? $model->note : "(なし)") ?>
            <?= Html::button('変更',['id'=>'toggle-btn-6','class'=>'btn btn-xs btn-default']) ?>
        </p>

        <div id="sub-menu-6" style="display:none" class="alert alert-info">
            <?php $form = \yii\bootstrap\ActiveForm::begin([
                'action'      => ['/purchase/update','id'=>$model->purchase_id],
                'fieldConfig' => [
                    'template' => "{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                    'horizontalCssClasses' => [
                    ],
                ],
            ]) ?>

            <?= $form->field($model, 'note')->textArea() ?>

            <?= Html::submitButton('更新') ?>

            <?php $form->end() ?>
        </div>

        <div id="sub-menu-7" style="display:none" class="alert alert-info">
            <?php $form = \yii\bootstrap\ActiveForm::begin([
                'action'      => ['/purchase/update','id'=>$model->purchase_id],
                'fieldConfig' => [
                    'template' => "{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                    'horizontalCssClasses' => [
                    ],
                ],
            ]) ?>

            <?= $form->field($model, 'customer_msg')->textArea() ?>

            <?= Html::submitButton('更新') ?>

            <?php $form->end() ?>
        </div>

    </div>

    <div class="row">

    <div class="col-md-6 col-sm-6 pull-right">
    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table table-striped table-bordered detail-view text-right'],
        'attributes' => [
            [
                'attribute' => 'subtotal',
                'format'    => 'currency',
            ],
            [
                'attribute' => 'tax',
                'format'    => 'currency',
            ],
            [
                'label'     => $model->getAttributeLabel('postage')
                            . Html::button('変更',['id'=>'toggle-btn-4','class'=>'btn btn-xs btn-default pull-right']),
                'attribute' => 'postage',
                'format'    => 'currency',
            ],
            [
                'attribute' => 'handling',
                'format'    => 'currency',
            ],
            [
                'attribute' => 'point_consume',
                'format'    => 'currency',
                'value'     => (0 - $model->point_consume),
            ],
            [
                'label'     => $model->getAttributeLabel('discount')
                            . Html::button('変更',['id'=>'toggle-btn-5','class'=>'btn btn-xs btn-default pull-right']),
                'attribute' => 'discount',
                'format'    => 'currency',
                'value'     => (0 - $model->discount),
            ],
            [
                'attribute' => 'total_charge',
                'format'    => 'raw',
                'value'     => Html::tag('strong', '￥' . number_format($model->total_charge),['class'=>'']),
            ],
            [
                'attribute' => 'receive',
                'format'    => 'currency',
            ],
            [
                'attribute' => 'change',
                'format'    => 'currency',
            ],
            [
                'attribute' => 'point_given',
                'format'    => 'integer',
            ],
        ],
    ]) ?>
    </div>

    <div class="col-md-6 col-sm-6 pull-left">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'company_id',
                'format'    => 'text',
                'value'     => $model->company ? strtoupper($model->company->key) : '(指定なし)',
            ],
            [
                'attribute' => 'branch_id',
                'format'    => 'html',
                'value'     => $model->branch->name,
            ],
            [
                'attribute' => 'customer_id',
                'format'    => 'html',
                'value'     => $model->customer ?
                                $model->customer->name :
                                ($model->delivery ?
                                    $model->delivery->name :
                                    ($recipe = $model->recipe ? $model->recipe->manual_client_name : '')),
            ],
            [
                'label' => 'Email',
                'format'    => 'raw',
                'value'     => ($model->customer ? $model->customer->email : $model->email)
                             . Html::button('変更',['id'=>'toggle-btn-8','class'=>'btn btn-xs btn-default pull-right'])
//               'visible'   => !($model->customer_id) ? true : false,
            ],
            [
                'attribute' => 'create_date',
                'format'    => 'raw',
                'value'     => $model->create_date
                             . Html::button('変更',['id'=>'toggle-btn-1','class'=>'btn btn-xs btn-default pull-right']),
            ],
            [
                'attribute' => 'shipping_date',
                'label'     => '発送日',
                'format'    => 'html',
                'value'     => sprintf('%s',$model->shipping_date ? $model->shipping_date : ''),
            ],
            [
                'attribute' => 'update_date',
                'label'     => '更新日',
                'format'    => 'html',
                'value'     => sprintf('%s (%s)',
                                       $model->update_date,
                                       ($s = $model->staff) ? $s->name01 : 'WEB'),
            ],
            [
                'attribute' => 'payment_id',
                'format'    => 'raw',
                'value'     => $model->payment->name
                             . Html::button('変更',['id'=>'toggle-btn-3','class'=>'btn btn-xs btn-default pull-right']),
            ],
            [
                'attribute' => 'status',
                'format'    => 'raw',
//              'value'     => $model->isExpired() ? Html::tag('p', $model->statusName, ['class'=>'alert-text alert-danger'])
//                             : $model->statusName . Html::button('変更',['id'=>'toggle-btn-2','class'=>'btn btn-xs btn-default pull-right']),
//                'value'     => $model->isPreparing() ? Html::tag('p', $model->statusName)
//                             : $model->statusName . Html::button('変更',['id'=>'toggle-btn-2','class'=>'btn btn-xs btn-default pull-right']),
                'value'     => $model->statusName . Html::button('変更',['id'=>'toggle-btn-2','class'=>'btn btn-xs btn-default pull-right']),

            ],
//            [
//                'label'     => '納品書金額表示',
//                'value'     => ($deliv = $model->delivery) ? $deliv->giftName : '表示',
//                'visible'   => $model->checkForGift(),
//            ],
        ],
    ]) ?>

        <div id="sub-menu-1" style="display:none" class="alert alert-info col-md-12">
            <?php $form = \yii\bootstrap\ActiveForm::begin([
                'action'      => ['/purchase/update','id'=>$model->purchase_id],
                'layout'      => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                    'horizontalCssClasses' => [
                        'label'   => 'col-sm-4',
                        'offset'  => 'col-sm-offset-1',
                        'wrapper' => 'col-sm-4',
                        'error'   => '',
                        'hint'    => '',
                    ],
                ],
            ]) ?>

            <?= $form->field($model, 'create_date')
                     ->widget(\yii\jui\DatePicker::className(), [
                        'language' => Yii::$app->language,
                        'clientOptions' =>[
                        'dateFormat'    => 'yyyy-MM-dd 00:00:00',
                        'language'      => Yii::$app->language,
                        'country'       => 'JP',
                        'showAnim'      => 'show',
                        'yearRange'     => 'c-1:c+0',
                        'changeMonth'   => true,
                        'changeYear'    => (1 == date('m', strtotime($model->create_date))) ? true : false,
                        'autoSize'      => true,
                        'showOn'        => "button",
                        'htmlOptions'=>[
                            'style'      => 'width:80px;',
                            'font-weight'=> 'x-small',
                            'class'      => 'form-group',
                        ],]]) ?>

            <p class="help-block"> 時刻は 00:00:00 に切り詰められます </p>

            <?= Html::submitButton('更新') ?>

            <?php $form->end() ?>
        </div>

        <div id="sub-menu-3" style="display:none" class="alert alert-info col-md-12">
            <?php $form = \yii\bootstrap\ActiveForm::begin([
                'action'      => ['/purchase/update','id'=>$model->purchase_id],
                'layout'      => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                    'horizontalCssClasses' => [
                        'label'   => 'col-sm-4',
                        'offset'  => 'col-sm-offset-1',
                        'wrapper' => 'col-sm-4',
                        'error'   => '',
                        'hint'    => '',
                    ],
                ],
            ]) ?>

            <?= $form->field($model, 'payment_id')->dropDownList($payments) ?>

            <?= Html::submitButton('更新') ?>

            <?php $form->end() ?>
        </div>

        <div id="sub-menu-2" style="display:none" class="alert alert-info col-md-12">
            <?php $form = \yii\bootstrap\ActiveForm::begin([
                'action'      => ['/purchase/update','id'=>$model->purchase_id],
                'layout'      => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                    'horizontalCssClasses' => [
                        'label'   => 'col-sm-4',
                        'offset'  => 'col-sm-offset-1',
                        'wrapper' => 'col-sm-4',
                        'error'   => '',
                        'hint'    => '',
                    ],
                ],
            ]) ?>

<!--            <?= $form->field($model, 'shipped')->dropDownList([1=>'発送済']) ?> -->
            <?= $form->field($model, 'shipped')->dropDownList([0=>'未発送',1=>'発送済', 9=>'発送不要']) ?>
            <?= $form->field($model, 'paid'   )->dropDownList([0=>'未入金',1=>'入金済']) ?>
            <?= $form->field($model, 'status' )->dropDownList($statusList,['class'=>'form-control col-md-3']) ?>

            <?= Html::submitButton('更新') ?>

            <?php $form->end() ?>
        </div>


        <div id="sub-menu-4" style="display:none" class="alert alert-info col-md-12">
            <?php $form = \yii\bootstrap\ActiveForm::begin([
                'action'      => ['/purchase/update','id'=>$model->purchase_id],
                'layout'      => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                    'horizontalCssClasses' => [
                        'label'   => 'col-sm-4',
                        'offset'  => 'col-sm-offset-1',
                        'wrapper' => 'col-sm-4',
                        'error'   => '',
                        'hint'    => '',
                    ],
                ],
            ]) ?>

            <?= $form->field($model, 'postage') ?>
            <?= $form->field($model, 'handling') ?>

            <?= Html::submitButton('更新') ?>

            <?php $form->end() ?>
        </div>

        <div id="sub-menu-5" style="display:none" class="alert alert-info col-md-12">
            <?php $form = \yii\bootstrap\ActiveForm::begin([
                'action'      => ['/purchase/update','id'=>$model->purchase_id],
                'layout'      => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                    'horizontalCssClasses' => [
                        'label'   => 'col-sm-4',
                        'offset'  => 'col-sm-offset-1',
                        'wrapper' => 'col-sm-4',
                        'error'   => '',
                        'hint'    => '',
                    ],
                ],
            ]) ?>

            <?= $form->field($model, 'discount') ?>

            <?= Html::submitButton('更新') ?>

            <?php $form->end() ?>
        </div>

        <div id="sub-menu-8" style="display:none" class="alert alert-info col-md-12">
            <?php $form = \yii\bootstrap\ActiveForm::begin([
                'action'      => ['/purchase/update','id'=>$model->purchase_id],
                'layout'      => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                    'horizontalCssClasses' => [
                        'label'   => 'col-sm-4',
                        'offset'  => 'col-sm-offset-1',
                        'wrapper' => 'col-sm-4',
                        'error'   => '',
                        'hint'    => '',
                    ],
                ],
            ]) ?>

            <?= $form->field($model, 'email') ?>

            <?= Html::submitButton('更新') ?>

            <?php $form->end() ?>
        </div>

    </div>

    </div>
    <?php if($model->commissions): ?>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query'  => $model->getCommissions(),
            'sort'  => ['defaultOrder'=>['commision_id'=>SORT_DESC]],
        ]),
        'layout'  => '{items}',
        'caption' => '手数料',
        'columns' => [
            [
                'attribute' => 'commision_id',
                'format'    => 'html',
                'value'     => function($data){ return Html::a(sprintf('%06d',$data->commision_id),['/commission/view','id'=>$data->commision_id]); }
            ],
            [
                'attribute' => 'company_id',
                'format'    => 'html',
                'value'     => function($data){ if($data->company) return strtoupper($data->company->key); },
            ],
            [
                'attribute' => 'customer_id',
                'format'    => 'html',
                'value'     => function($data){ if($data->customer) return Html::a($data->customer->name,['/customer/view','id'=>$data->customer_id]); },
            ],
            [
                'attribute' => 'fee',
                'format'    => 'currency',
                'contentOptions' => ['class' => 'text-right'],
            ],
        ],
    ]) ?>
    <?php endif ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getMails(),
            'sort'  => ['defaultOrder'=>['mailer_id'=>SORT_DESC]],
        ]),
        'id'      => 'mail-log',
        'layout'  => '{items}',
        'caption' => 'メール送信履歴',
        'columns' => [
            [
                'attribute' => 'date',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->date, ['/mail-log/view', 'id'=>$data->mailer_id]); },
            ],
            'subject',
        ],

    ]) ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query'  => \common\models\Recipe::find()->andwhere(['recipe_id'=>
                        \common\models\LtbPurchaseRecipe::find()->andwhere(['purchase_id'=>$model->purchase_id])->select('recipe_id')])
        ]),
        'layout'  => '{items}',
        'caption' => '適用書',
        'columns' => [
            [
                'attribute' => 'recipe_id',
                'format'    => 'html',
                'value'     => function($data){ return Html::a(sprintf('%06d', $data->recipe_id),['/recipe/admin/view','id'=>$data->recipe_id]); }
            ],
            [
                'attribute' => 'homoeopath.homoeopathname',
                'label'     => 'ホメオパス',
            ],
            [

                'attribute' => 'client.name',
                'label'     => 'クライアント',
                'value'     => function($data) {
                    return ($data->client && $data->client->name)
                             ? $data->client->name
                             : $data->manual_client_name;
                }
            ],
            [
                'attribute' => 'create_date',
                'format'    => ['date','php:Y-m-d D H:i'],
            ],
        ],
    ]) ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query'  => ChangeLog::find()->where(['tbl' => $model->tableName(),'pkey'=>$model->purchase_id]),
            'sort'  => ['defaultOrder' => ['create_date'=>SORT_DESC]],
        ]),
        'layout'  => '{pager}{items}',
        'caption' => 'DB操作履歴',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'create_date',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->create_date,['/change-log/view','id'=>$data->create_date]); }
            ],
            'route',
            'action',
            'user.name',
        ],

    ]) ?>

</div>
