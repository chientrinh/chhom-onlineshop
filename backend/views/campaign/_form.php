<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Vegetable;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/vegetable/_form.php $
 * $Id: _form.php 2931 2016-10-07 04:44:08Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Vegetable
 * @var $form yii\widgets\ActiveForm
 */

$jscode = "

    $('.js-zenkaku-to-hankaku').on('change', function(){
        $(this).val(charactersChange($(this).val()));
    });

    charactersChange = function(val){
        var han = val.replace(/[Ａ-Ｚａ-ｚ０-９：]/g,function(s){return String.fromCharCode(s.charCodeAt(0)-0xFEE0)});

        if(val.match(/[Ａ-Ｚａ-ｚ０-９：]/g)){
            return han;
        }

        return val;
    }
// キャンペーンコードの再作成
create_privateid = function( n ){
    var CODE_TABLE = '0123456789'
        + 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
        + 'abcdefghijklmnopqrstuvwxyz';
    var r = '';
    for (var i = 0, k = CODE_TABLE.length; i < n; i++){
        r += CODE_TABLE.charAt(Math.floor(k * Math.random()));
    }
    return r;
}

$('input[name=code-prefix]').on('change', function(){
    var campaign_code = $('#campaign_code_text').text();
    var changed_code = prefixChange(campaign_code);
    $('[name=\"Campaign[campaign_code]\"').val(changed_code);
     $('#campaign_code_text').text(changed_code);
});

prefixChange = function(val) {
    var prefix = $('input[name=code-prefix]:checked').val();
    $('[name=\"Campaign[campaign_type]\"').val(getType(prefix));

    if(val.indexOf(prefix) != 0){
        val = prefix + val.slice(1)

        return val;
    } 

    return val;
}

getType = function(val) {
    var type = 1;
//    console.log(val);
    switch(val) {
        case 'd':
            type = 1;
            break;
        
        case 'p':
            type = 2;
            break;
        default:
            type = 1;
    }
    return type;
}

// 「コード再作成」ボタン押下時
$('#code-regenerate').on('click', function() {
    var code = create_privateid(8);
    var prefix = $('#code-prefix:checked').val();
    code = prefix+code;
    $('[name=\"Campaign[campaign_code]\"]').val(code);
    $('[name=\"Campaign[campaign_type]\"').val(getType(prefix));
    $('#campaign_code_text').text(code);
});
"; 
$this->registerJs($jscode);

$csscode = "
    .form-group { width:75%; }
    .date-input { margin-bottom: 20px;}
";

$this->registerCss($csscode);
?>

<div class="campaign-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="form-group field-campaign-campaign_code">
        <?php //Html::activeLabel($campaign, 'campaign_code', []); ?>
        <?= $form->field($campaign, 'campaign_code')->hiddenInput(['class'=>'form-control']); ?>
        <?= $form->field($campaign, 'campaign_type')->hiddenInput(['value' => $campaign->isNewRecord ? 1 :$campaign->campaign_type ,'class'=>'form-control'])->label(false); ?>

        <?= Html::tag('p', $campaign->campaign_code, ['id' => 'campaign_code_text']); ?>
        <?= Html::activeHint($campaign, 'campaign_code', []); ?>
        <?php //if($campaign->isNewRecord): ?>
            <?= Html::tag('span', 'キャンペーンのタイプを選択してください'); ?>
            <?= Html::radioList('code-prefix',null,['d'=>'値引', 'p'=>'ポイント'],['itemOptions' => ['id' => 'code-prefix']]); ?>
            <?= Html::button('コード再作成',['id'=>'code-regenerate','class'=>'btn btn-sm btn-success']) ?>
        <?php //endif ?>
    </div>

    <?= $form->field($campaign, 'campaign_name')->textInput(['class'=>'form-control js-input-label', 'maxlength' => true]) ?>

    <?= $form->field($campaign, 'branch_id')->dropDownList(ArrayHelper::map(\common\models\Branch::find()->forCampaign()->All(), 'branch_id', 'name')); ?>

    <?= $form->field($campaign, 'streaming_id')->dropDownList($streamings); ?>
    <?= $form->field($campaign, 'free_shipping1')->dropDownList($campaign->shippings); ?>
    <?= $form->field($campaign, 'free_shipping2')->dropDownList($campaign->shippings); ?>
    <?= $form->field($campaign, 'pre_order')->dropDownList($campaign->preorders); ?>


    <?= $form->field($campaign, 'start_date')
             ->widget(\yii\jui\DatePicker::classname(), [
                'language'   => 'ja',
                'dateFormat' => 'yyyy-MM-dd 00:00:00',
                'options'    => ['class'=>'form-control col-md-12 date-input'],
    ]) ?>

    <?= $form->field($campaign, 'end_date')
             ->widget(\yii\jui\DatePicker::classname(), [
                'language'   => 'ja',
                'dateFormat' => 'yyyy-MM-dd 23:59:59',
                'options'    => ['class'=>'form-control col-md-12 date-input'],
    ]) ?>

    <?= $form->field($campaign, 'status')->dropDownList($campaign->statuses) ?>

    <div class="form-group">
        <?= Html::submitButton($campaign->isNewRecord ? '登録' : '更新', ['class' => $campaign->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

        <?php if(! $campaign->isNewRecord): ?>
        <?= Html::a('削除', ['delete', 'id' => $campaign->campaign_id], [
            'class' => 'btn btn-danger pull-right',
            'data' => [
                'confirm' => '本当にこのキャンペーン情報を削除してもいいですか',
            ],
        ]) ?>
        <?php endif ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
