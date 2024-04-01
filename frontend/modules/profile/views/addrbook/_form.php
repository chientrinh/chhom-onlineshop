<?php

use \yii\bootstrap\ActiveForm;
use \yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/views/addrbook/_form.php $
 * $Id: _form.php 3970 2018-07-13 08:46:33Z mori $
 */

$prefs = \yii\helpers\ArrayHelper::map(\common\models\Pref::find()->where(['pref_id'=>range(1,47)])->all(), 'pref_id', 'name');
array_unshift($prefs, "都道府県を選択");
$readonly = false;
$disabled = 'false';
$code_submit_disabled = true;

if($direct_flg)// && $direct_customer && $direct_customer->code ) {
    $readonly = true;
    $disabled = 'disabled';
//}

if($model->code)
    $code_submit_disabled = false;

// 既存の会員証NO
$old_code = $model->id ? \common\models\CustomerAddrbook::findOne(['id' => $model->id])->code : null;

$jscode = "
    $('#customeraddrbook-code').on('change', function() {
        if($('#customeraddrbook-code').val().length > 0) {
            $('#direct_flg').prop('checked',true).trigger('change');
        } else {
            $('#direct_flg').prop('checked',false).trigger('change');
        }
    });

    $('#direct_flg').on('change', function() {
        if($('#direct_flg').prop('checked')) {
            $('#code-submit').attr('disabled',false);
            $('#customeraddrbook-code').attr('readonly', false);
            $('#customeraddrbook-name01').attr('readonly',true);
            $('#customeraddrbook-name02').attr('readonly',true);
            $('#customeraddrbook-kana01').attr('readonly',true);
            $('#customeraddrbook-kana02').attr('readonly',true);
            $('#customeraddrbook-zip01').attr('readonly',true);
            $('#customeraddrbook-zip02').attr('readonly',true);
            $('#zip-addr').attr('disabled',true);
            $('#customeraddrbook-pref_id').attr('readonly',true);
            $('#customeraddrbook-addr01').attr('readonly',true);
            $('#customeraddrbook-addr02').attr('readonly',true);
            $('#customeraddrbook-tel01').attr('readonly',true);
            $('#customeraddrbook-tel02').attr('readonly',true);
            $('#customeraddrbook-tel03').attr('readonly',true);
            $('#submit').attr('disabled',true);
        } else {
            $('#code-submit').attr('disabled',true);
            $('#customeraddrbook-code').val('');
            $('#customeraddrbook-code').attr('readonly',true);
            $('#customeraddrbook-name01').attr('readonly',false);
            $('#customeraddrbook-name02').attr('readonly',false);
            $('#customeraddrbook-kana01').attr('readonly',false);
            $('#customeraddrbook-kana02').attr('readonly',false);
            $('#customeraddrbook-zip01').attr('readonly',false);
            $('#customeraddrbook-zip02').attr('readonly',false);
            $('#zip-addr').attr('disabled',false);
            $('#customeraddrbook-pref_id').attr('readonly',false);
            $('#customeraddrbook-addr01').attr('readonly',false);
            $('#customeraddrbook-addr02').attr('readonly',false);
            $('#customeraddrbook-tel01').attr('readonly',false);
            $('#customeraddrbook-tel02').attr('readonly',false);
            $('#customeraddrbook-tel03').attr('readonly',false);
            $('#submit').attr('disabled',false);
        }
    });
";
$this->registerJs($jscode);

?>
<script>
function deleteAddr() {
    if (!confirm('住所録を削除してよろしいですか？')) {
        return false;
    }
    $("<input>", {
        type: 'hidden',
        name: 'id',
        value: <?= $model->id ?>
    }).appendTo('form#form-delete');

    $('#form-delete').submit();
}
</script>

<?php $form = ActiveForm::begin([
  'id' => 'form-signup',
  'layout' => 'default',
  'validateOnBlur'  => false,
  'validateOnChange'=> false,
  'validateOnSubmit'=> false,
  'fieldConfig'     => ['template'=>'{input}{error}'],
  'method' => 'POST'
]);?>

<table summary="<?= $title ?>" id="FormTable" class="table table-bordered">
<tbody>
    <?php if(isset($model->customer) && ($model->customer->grade_id >= \common\models\CustomerGrade::PKEY_TA)): ?>
    <tr>
    <th><div>
    <label>会員証NO</label>
    </div></th>
    <td>
<div>
      <input type="checkbox" id="direct_flg" name="direct_flg" value="1" <?= $direct_flg ? "checked=checked" : ""?>>会員証を入力して直送先を登録する
            <?php //Html::checkbox('direct_flg', [1=>'会員証を入力して直送先を登録する'], ['value' => Yii::$app->request->post('direct_flg', 1), 'label' => '会員証を入力して直送先を登録する']) ?>
</div>
        <div class='col-md-3'>
            <?= $form->field($model, 'code')->textInput(['placeholder' => '会員証NOを入力してください', 'class'=>'form-control js-zenkaku-to-hankaku', 'readonly' => $code_submit_disabled]) ?>
            <?= $old_code ? "変更前の会員証NO：".$old_code : ""?>
         </div>
        <div class="form-group">
        <?= Html::submitButton('会員情報検索', ['id' => 'code-submit', 'class' => 'btn btn btn-primary', 'name'  => 'scenario',
            'value' => 'code', 'disabled' => $code_submit_disabled]) ?>
        </div>
    </td>
    </tr>
    <?php endif ?>
    
    <tr>
    <th><div class="required"><label>お名前</label></div></th>
    <td>
    <span class="float-box2">姓</span>
    <?= $form->field($model, 'name01',['options'=>['class'=>'Name']])->textInput(['readonly' => $readonly]) ?>
    <span class="float-box2">名</span>
    <?= $form->field($model, 'name02',['options'=>['class'=>'Name']])->textInput(['readonly' => $readonly]) ?>
    </td>
    </tr>

    <tr>
    <th><div class="required">
    <label>お名前（ふりがな）</label>
    </div></th>
    <td>
    <span class="float-box2">せい</span>
    <?= $form->field($model, 'kana01',['options'=>['class'=>'Name']])->textInput(['readonly' => $readonly]) ?>
    <span class="float-box2">めい</span>
    <?= $form->field($model, 'kana02',['options'=>['class'=>'Name']])->textInput(['readonly' => $readonly]) ?>
    </td>
    </tr>

    <tr>
    <th><div class="required">
    <label>郵便番号</label>
    </div></th>
    <td><div class="field-changeform-zip"> <span class="float-box2">〒</span>
    <?= $form->field($model, 'zip01',['options'=>['class'=>'Zip']])->textInput(['class'=>'form-control js-zenkaku-to-hankaku', 'readonly' => $readonly]) ?>
    <span class="float-box">-</span>
    <?= $form->field($model, 'zip02',['options'=>['class'=>'Zip']])->textInput(['class'=>'form-control js-zenkaku-to-hankaku', 'readonly' => $readonly]) ?>
    &nbsp;
    <button type="submit" id="zip-addr" class="btn btn-primary" name="scenario" value="zip2addr"<?= $readonly ? "disabled='disabled'" : "" ?>>住所を検索</button>
    &nbsp;
<a href="http://www.post.japanpost.jp/zipcode/" class="btn btn-default pull-right" target="_blank"><span class="fs12">郵便番号検索へ</span></a>
    <p class="help-block help-block-error"></p>
    </div></td>
    </tr>

    <tr>
    <th><div class="required">
    <label>住所</label>
    </div></th>
    <td>
    <?= $form->field($model, 'pref_id')->dropDownList($prefs, ['readonly' => $readonly,'style' =>  $readonly ? 'pointer-events:none;' : ""]) ?>
    <label class="control-label" for="signupform-addr01">市区町村名（例：千代田区神田神保町）</label>
<?php if(isset($candidates) && (false !== $candidates)):
echo $form->field($model, 'addr01')->dropDownList($candidates,['readonly' => $readonly])->render();
?>

<?php else: ?>
    <?= $form->field($model, 'addr01')->textInput(['readonly' => $readonly]) ?>
<?php endif ?>

    <label class="control-label" for="signupform-addr02">番地・ビル名（例：1-3-5）</label>
    <?= $form->field($model, 'addr02')->textInput(['readonly' => $readonly]) ?>
    </td>
    </tr>

    <tr>
    <th><div class="required">
    <label>電話番号</label>
    </div></th>
    <td>
    <?= $form->field($model, 'tel01',['options'=>['class'=>'Tel']])->textInput(['class'=>'form-control js-zenkaku-to-hankaku', 'readonly' => $readonly]) ?>
    <span class="float-box">-</span>
    <?= $form->field($model, 'tel02',['options'=>['class'=>'Tel']])->textInput(['class'=>'form-control js-zenkaku-to-hankaku', 'readonly' => $readonly]) ?>
    <span class="float-box">-</span>
    <?= $form->field($model, 'tel03',['options'=>['class'=>'Tel']])->textInput(['class'=>'form-control js-zenkaku-to-hankaku', 'readonly' => $readonly]) ?>
    </td>
    </tr>

</tbody>
</table>

    <div class="form-group">

    <?= Html::submitButton($model->isNewRecord ? "追加" : "更新", [
        'class' => 'btn btn-primary',
        'id' => 'submit',
        'name'  => 'scenario',
        'value' => 'default',
    ]) ?>
    <?= Html::a('戻る', ['index'], ['class'=>'btn btn-default']) ?>

    </div><!--form-group-->
    <?php ActiveForm::end(); ?>

    <?php if(! $model->isNewRecord): ?>
        <?php $form = ActiveForm::begin([
            'action' => 'delete',
            'id' => 'form-delete',
            'method' => 'POST'
          ]);?>
            <?= Html::a('削除', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger pull-right',
                'onclick' => 'return deleteAddr();'
            ]) ?>
        <?php ActiveForm::end(); ?>
    <?php endif ?>
