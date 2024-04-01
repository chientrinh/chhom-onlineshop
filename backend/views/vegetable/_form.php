<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\Vegetable;
use common\models\SalesCategory;
use common\models\SalesCategory1;
use common\models\SalesCategory2;
use common\models\SalesCategory3;
use common\models\Company;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/vegetable/_form.php $
 * $Id: _form.php 2931 2016-10-07 04:44:08Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Vegetable
 * @var $form yii\widgets\ActiveForm
 */

$salesInfo = SalesCategory::find()->where(['sku_id' => $model->getSkuId()])->one();
if($salesInfo) {
    $sales1Info = $salesInfo->sales1;
    $sales2Info = $salesInfo->sales2;
    $sales3Info = $salesInfo->sales3;
    if($sales1Info && $sales2Info && $sales3Info) {
        $model->bunrui_code1 = $sales1Info->bunrui_id -1;
        $model->bunrui_code2 = $sales2Info->bunrui_id -1;
        $model->bunrui_code3 = $sales3Info->bunrui_id -1;
        $model->vender_key = Company::find()->where(['key' => $salesInfo->vender_key])->one()->company_id - 1;
    } else {
        $model->bunrui_code1 = 0;
        $model->bunrui_code2 = 0;
        $model->bunrui_code3 = 0;
        $model->vender_key = 0;
    }
} else {
    $model->bunrui_code1 = 0;
    $model->bunrui_code2 = 0;
    $model->bunrui_code3 = 0;
    $model->vender_key = 0;
}


$sales1 = SalesCategory1::find()->asArray()->all();
$sales2 = SalesCategory2::find()->asArray()->all();
$sales3 = SalesCategory3::find()->asArray()->all();
$salesArray1 = ArrayHelper::getColumn($sales1, function ($element) {
    return $element['bunrui_code1']." ".$element['name'];
});
$salesArray2 = ArrayHelper::getColumn($sales2, function ($element) {
    return $element['bunrui_code2']." ".$element['name'];
});
$salesArray3 = ArrayHelper::getColumn($sales3, function ($element) {
    return $element['bunrui_code3']." ".$element['name'];
});

$companies = ArrayHelper::getColumn(Company::find()->asArray()->all(), function ($element) {
    return $element['key']." ".$element['name'];
});


$jscode = "

    $('.js-zenkaku-to-hankaku').on('change', function(){
        $(this).val(charactersChange($(this).val()));
    });


    // 入力された値を印刷用名称に反映する
    // $('.js-input-label').on('change', function(){
        // target = $('#vegetable-print_name').val();

        // if ($('#vegetable-print_name').val().length > 0)
        //     return ;

        // changePrintName();
    // });

    changePrintName = function (){
        var str = ''; // 印刷用名称
        $('#vegetable-print_name').val('');
        $('.js-input-label').each(function(index) {
            val = $(this).val();

            switch (index) {
                case 0　: // 種別
                    if (val == 1)
                        str += '". Vegetable::DIV_1. "';
                    else 
                        str += '". Vegetable::DIV_0. "';

                    break;

                case 3　: // 容量
                    if (val.length != 0) {
                        str += ' ' + val + 'g'; // 印刷用名称に追加する
                    }

                    break;

		case 4 : // 表示順。これは反映させない
		    break;
                case 5 :
		    break;
                case 6 :
		    break;
                case 7 :
		    break;
                case 8 :
		    break;

                default :
                    str += val;
                    break;
            }

        });
        // 印刷用名称に反映する
        $('#vegetable-print_name').val(str);
    }


    charactersChange = function(val){
        var han = val.replace(/[Ａ-Ｚａ-ｚ０-９：]/g,function(s){return String.fromCharCode(s.charCodeAt(0)-0xFEE0)});

        if(val.match(/[Ａ-Ｚａ-ｚ０-９：]/g)){
            return han;
        }

        return val;
    }
"; 
$this->registerJs($jscode);

$csscode = "
    .form-group { width:75%; }
    #update_print_name {
        width: 7%;
        margin: 0 0 15px 0;

    }
";

$this->registerCss($csscode);
?>

<div class="vegetable-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php //= $form->field($model, 'veg_id')->textInput(['maxlength' => true, 'disabled'=> true/*! $model->isNewRecord*/]) ?>

    <?= $form->field($model, 'is_other')->radioList(array('0'=>'野菜', '1' =>'その他商品')) ?>

    <?= $form->field($model, 'division')->dropDownList(\common\models\Vegetable::getDivision(), ['class'=>'form-control js-input-label']) ?>

    <?= $form->field($model, 'origin_area')->textInput(['class'=>'form-control js-input-label', 'maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['class'=>'form-control js-input-label', 'maxlength' => true]) ?>

    <?= $form->field($model, 'kana')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'capacity')->textInput(['class'=>'form-control js-input-label js-zenkaku-to-hankaku', 'maxlength' => true]) ?>

    <?= $form->field($model, 'dsp_priority')->textInput(['class'=>'form-control js-input-label js-zenkaku-to-hankaku', 'maxlength' => true]) ?>

    <?= $form->field($model, 'vender_key')->dropDownList($companies, ['class'=>'form-control js-input-label']) ?>
    <?= $form->field($model, 'bunrui_code1')->dropDownList($salesArray1, ['class'=>'form-control js-input-label']) ?>
    <?= $form->field($model, 'bunrui_code2')->dropDownList($salesArray2, ['class'=>'form-control js-input-label']) ?>
    <?= $form->field($model, 'bunrui_code3')->dropDownList($salesArray3, ['class'=>'form-control js-input-label']) ?>

    <?= Html::button($content = '名称反映', ['class'=>'form-control btn btn-warning', 'id' => 'update_print_name', 'title'=>'クリックすると、上記項目で入力した内容が反映されます', 'onClick'=>'changePrintName()']) ?>

    <?= $form->field($model, 'print_name')->textInput(['class'=>'form-control js-input-label', 'maxlength' => true]) ?>
    <!-- <div class="form-group required">
        <label class="control-label" for="print-name">印刷用名称</label>
        <div class="form-inline">
            <?= Html::textInput('Vegetable[print_name]', $model->print_name, ['class'=>'form-control', 'id' => 'vegetable-print-name', 'maxlength'=> 255, 'style'=>'width:70%'])?>
        </div>
        <div class="hint-block">印刷時に出力される名称です。<br>上部項目入力時、又は右部の「名称更新」ボタンで入力内容が反映され、当項目内で変更も可能です。</div>
    </div> -->

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '追加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

        <?php if(! $model->isNewRecord): ?>
        <?= Html::a('削除', ['delete', 'id' => $model->veg_id], [
            'class' => 'btn btn-danger pull-right',
            'data' => [
                'confirm' => '本当にこの野菜を削除してもいいですか',
            ],
        ]) ?>
        <?php endif ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
