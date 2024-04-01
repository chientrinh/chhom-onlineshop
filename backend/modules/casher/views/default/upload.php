<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>
<p>
<?php
$jscode = "
    $(function() {
        $('#csvuploadmultiform-csvfiles').on('change', function() {
            var file = this.files[0];
            if(file != null) {
                console.log(file.name); // ファイル名をログに出力する
                $('#upload').removeAttr('disabled');
            } else {
                $('#upload').attr('disabled', 'disabled');
            }
        });
    });
";

$this->registerJs($jscode);

 $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
     echo $form->field($model, 'csvFiles[]')->fileInput(['multiple' => true, 'accept' => 'text/csv', 'style' => 'margin-left:16%;'])->label('売上CSV');
?>


<div class="form-group" style="margin-left:16%;">
    <?= Html::submitButton('送信', ['id' => 'upload', 'class' => 'btn btn btn-primary', 'disabled' => 'disabled']) ?>
</div>
<?php ActiveForm::end() ?>
