<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>
<p>
<?php
 $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
 if(isset($model->file)) {
     $model->file_name = $model->file->name;
     echo "セットしたファイル： <strong id='file_name'>".$model->file_name."</strong>";
     echo $form->field($model, 'file_name')->hiddenInput()->label(false);
 }
     echo $form->field($model, 'file')->fileInput()->label('前月分の振替結果CSV');

?>


<div class="form-group">
    <?= Html::submitButton('送信', ['id' => 'upload', 'class' => 'btn btn btn-primary', 'disabled' => 'disabled']) ?>
</div>
<php ActiveForm::end() ?>
