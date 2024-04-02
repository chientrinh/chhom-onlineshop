<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/member/views/toranoko/update.php $
 * $Id: update.php 3075 2016-11-09 06:49:32Z mori $
 * @var $this     \yii\web\View
 * @var $model    UpdateForm
 * @var $customer ViewForm
 */

use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;
use common\models\Membership;
use common\models\Payment;
use common\models\Product;

$jscode = sprintf('

var mid = $("input[type=radio]:checked").val();
if (mid == %s)
    $("#issues").hide();

%s;

$("input[type=radio]").click(function() {
    if(%s == $(this).val())
        $("#issues").hide();
    else
        $("#issues").show();
}); 
', Membership::PKEY_TORANOKO_NETWORK
 , Yii::$app->request->get('mid', 0) ? '$("input[type=radio]:not(:checked)").prop("disabled", true)' : null
 , Membership::PKEY_TORANOKO_NETWORK);

$this->registerJs($jscode);

if($customer->isMember() || $customer->wasMember())
    $status = '更新';
else
    $status = '入会';

$title = ArrayHelper::getValue($this,'context.title', "会員");
$this->title = sprintf('%s手続き | %s | %s | %s', $status, $customer->name, $title , Yii::$app->name);

$mships = Membership::find()->where([
    'membership_id' => [ Membership::PKEY_TORANOKO_GENERIC,
                         Membership::PKEY_TORANOKO_NETWORK,]
])->asArray()->select(['membership_id','name'])->all();
$mships = ArrayHelper::map($mships,'membership_id','name');

$issues = Product::find()
        ->active()
        ->andWhere(['like','name','Oasis%',false])
        ->all();
$issues = ArrayHelper::map($issues, 'product_id', 'name');

if(null === $model->issues)
    if(Membership::PKEY_TORANOKO_GENERIC == Yii::$app->request->get('mid', 0))
        $model->issues = array_keys($issues);
?>

<div class="customer-default-create col-md-12">
    <h1>
        <?= $title ?><small> <?= $status ?> 手続き</small>
    </h1>

    <?= \yii\widgets\DetailView::widget([
        'model'      => $customer,
        'template'   => '<tr><th class="col-md-2 col-sm-2">{label}</th><td class="col-md-10 col-sm-6">{value}</td></tr>',
        'attributes' => [
            [
                'label'    => Html::tag('div',Html::tag('label',$model->getAttributeLabel('kana'))),
                'format'   => 'html',
                'value'    => Html::a($customer->kana,['view','id'=>$customer->customer_id]),5
            ],
            [
                'attribute' => 'code',
            ],
            [
                'attribute' => '状態',
                'value'     => $customer->isMember()
                             ? sprintf('%s (%sまで)',$customer->membership->name,Yii::$app->formatter->asDate($customer->membership->expire_date))
                             : ($customer->wasMember() ? "期限切れ" : "未入会"),
            ],
        ]]) ?>

    <?php $form = \yii\bootstrap\ActiveForm::begin([
        'id' => 'member-form',
        'layout' => 'default',
        'validateOnBlur'   => false,
        'validateOnChange' => false,
        'validateOnSubmit' => false,
        'fieldConfig' => [
            'template' => '{input}{hint}{error}',
            'inputOptions' => ['class' => 'form-control col-md-4'],
        ],
    ]) ?>

    <?php if($customer->hasErrors()): ?>
    <div class="col-md-12 alert alert-error">
            <?= Html::tag('div',$customer->getAttributeLabel('kana')) ?>

        <div class="col-md-6 col-xs-4">
    <?= $form->field($customer,'kana01',['inputTemplate' => '<div class="input-group"><span class="input-group-addon">せい</span>{input}</div>'])->textInput() ?>
        </div>
        <div class="col-md-6 col-xs-4">
    <?= $form->field($customer,'kana02',['inputTemplate' => '<div class="input-group"><span class="input-group-addon">めい</span>{input}</div>']) ?>
        </div>
        <div class="col-md-12 col-xs-8">
            <?= Html::tag('div',$customer->getAttributeLabel('tel')) ?>

            <?= Html::tag('div', $form->field($customer, 'tel01')->textInput(['class'=>'form-control js-zenkaku-to-hankaku']), ['class'=>'col-md-3']) ?>
            <?= Html::tag('div', $form->field($customer, 'tel02')->textInput(['class'=>'form-control js-zenkaku-to-hankaku']), ['class'=>'col-md-3']) ?>
            <?= Html::tag('div', $form->field($customer, 'tel03')->textInput(['class'=>'form-control js-zenkaku-to-hankaku']), ['class'=>'col-md-3']) ?>
        </div>
    </div>
    <?php endif ?>

    <div class="row col-md-12 col-xs-8">
        <div class="col-md-6 col-xs-4 alert bg-warning">
            <?= $form->field($model,'membership_id')->inline(true)->radioList($mships,['separator'=>'<br>']) ?>
        </div>
        <div id="issues" class="col-md-6 col-xs-4 alert bg-info" style="<?= ($model->membership_id === Membership::PKEY_TORANOKO_NETWORK) ? 'display:none' : null ?>">
            <?= $form->field($model,'issues')->inline(true)->checkboxList($issues,['separator'=>'<br>']) ?>
        </div>
    </div>
    
    <div class="row col-md-6 col-xs-4">
        <label><?= $model->getattributeLabel('payment_id') ?></label>

        <?php if(! $model->hasErrors('payment_id')): ?>
            <p><?= Payment::findOne($model->payment_id)->name ?></p>
        <?php else: ?>
            <?= $form->field($model,'payment_id')->label(false)->inline(true)->radioList(ArrayHelper::map($payments, 'payment_id', 'name'),['separator'=>'<br>']) ?>
        <?php endif ?>

        <p>&nbsp;</p>
        <?= Html::submitButton('更新する',['class'=>'btn btn-success']) ?>
    </div>

    <div class="col-md-12 alert alert-error">
        <?= $form->errorSummary($model) ?>
    </div>

    <?php $form->end() ?>

</div>
