<?php
/**
 * $URL: $
 * $Id: $
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\RemedyCategory;
use common\models\RemedyCategoryDescription;


$array_display = ['1' => '表示', '0' => '非表示'];

// settings for image slider
$jscode = "

    // 初期表示
    var descObj = $( 'input[name=\"RemedyCategoryDescription[desc_division]\"]:radio:checked' );
    switchShowCategory(descObj.val());

    // 説明区分 選択時
    $( 'input[name=\"RemedyCategoryDescription[desc_division]\"]:radio' ).change( function() {
        switchShowCategory($(this).val());
    });

    function switchShowCategory(desc) {

        var categoryObj = $('.for-ad');

        // 説明区分が2（補足）でない場合はカテゴリー選択を表示しない
        if (desc != '".RemedyCategoryDescription::DIV_REPLETION."') {

            categoryObj.hide();
            categoryObj.attr('disabled', true);

            categoryObj.siblings().hide();

            categoryObj.parent().hide();
        } else {
            categoryObj.show();
            categoryObj.removeAttr('disabled');
            categoryObj.addClass(\"form-control\");

            categoryObj.siblings().show();

            categoryObj.parent().show();
            categoryObj.parents().find('div').addClass('required');
        }
    }
";
$this->registerJs($jscode, \yii\web\View::POS_LOAD);

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="remedy-potency-form">

    <?php
        $form = ActiveForm::begin([
            'fieldConfig' => [
            'template' => "{label}{input}\n{hint}\n{error}",
            ],
            'validateOnBlur'  => false,
            'validateOnChange'=> false,
            'validateOnSubmit'=> false,
        ]);

        echo $form->field($model, 'desc_division')->radioList(RemedyCategoryDescription::getDivisionForView(), ['class'=>'desc']);
        echo $form->field($model, 'remedy_category_id')->dropDownList(RemedyCategory::getRemedyCategoryPulldown(), ['class'=>'form-control']);
        echo $form->field($model, 'title')->textInput(['maxlength' => 255, 'class' => 'for-ad form-control']);
        echo $form->field($model, 'body')->textarea(['rows'=>15]);
        echo $form->field($model, 'seq')->dropDownList(range(0, 99), ['class'=>'form-control']);
        // echo $form->field($model, 'seq')->textInput(['placeholder' => '0', 'maxlength' => 11]);
        echo $form->field($model, 'is_display')->dropDownList($array_display, ['class'=>'form-control']);
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '登録' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

    <?php if (! $model->isNewRecord ): ?>
    <div class="pull-right">
        <?= Html::a("削除", ['delete', 'id' => $model->remedy_category_desc_id], ['class' => 'btn btn-danger','data-confirm'=>'補足説明を削除します。よろしいですか。']) ?>
    </div>
    <?php endif; ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
