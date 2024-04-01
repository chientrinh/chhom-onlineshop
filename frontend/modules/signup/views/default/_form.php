<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use \yii\helpers\ArrayHelper;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/signup/views/default/_form.php $
 * $Id: _form.php 3919 2018-06-05 00:37:42Z mori $
 *
 * @var $this yii\web\View
 * @var $form yii\bootstrap\ActiveForm
 * @var $model \frontend\models\SignupForm
 */

// for pref_id
$prefs = ArrayHelper::map(\common\models\Pref::find()->where(['pref_id'=>range(1,47)])->all(), 'pref_id', 'name');
array_unshift($prefs, "都道府県を選択");

// for sex_id
$sexes = ArrayHelper::map(\common\models\Sex::find()->all(), 'sex_id', 'name');
$sexes[0] = '';

// for birth_y
$years[0]='';
foreach(range(1900, date('Y')) as $y)
    $years[$y] = $y;

// for birth_m
$months = range(1, 12);
$months = array_merge([0=>''],array_combine($months,$months));

// for birth_d
$days = range(1, 31);
$days = array_merge([0=>''],array_combine($days, $days));

// for subscribe
$radioList = ArrayHelper::map(\common\models\Subscribe::find()->all(), 'subscribe_id','name');

if(isset($candidates))
{
    $suggest = [];
    $suggest['email'] = array_unique(ArrayHelper::getColumn($candidates, 'email', false));
    $suggest['addr']  = array_unique(ArrayHelper::getColumn($candidates, function($data){ return $data->postnum . $data->pref_id . $data->address1; }));

}

?>

  <div class="row column01">
  <div class="col-md-12">

<?php $form = ActiveForm::begin([
  'id' => 'form-signup',
  'layout' => 'default',
  'validateOnBlur'  => false,
  'validateOnChange'=> false,
  'validateOnSubmit'=> false,
  'fieldConfig'     => ['template'=>'{input}{hint}{error}'],
]);?>

<table summary="会員登録" id="FormTable" class="table table-bordered">
    <caption>
        <span style="color:red"> * </span>印は必須記入項目です。
    </caption>
<tbody>

    <!--
    <tr>
        <th style="background: #fcf8e3;"><div><label>キャンペーンコード</label></div></th>
        <td>
            <?= Html::textInput("campaign_code", Yii::$app->request->post('campaign_code'), ['class' => 'form-control', 'style' => 'width:30%;']) ?>
        </td>
    </tr>
    -->
    <tr>
    <th><div class="required"><label>お名前</label></div></th>
    <td>
    <?php if('create' == $this->context->action->id): ?>

      <span class="float-box2">姓</span>
      <?= $form->field($model, 'name01',['options'=>['class'=>'']]) ?>
      <span class="float-box2">名</span>
      <?= $form->field($model, 'name02',['options'=>['class'=>'']]) ?>

    <?php else: ?>

      <?= $model->name ?>

    <?php endif ?>
    </td>
    </tr>

    <tr>
    <th><div class="required">
    <label>お名前（ふりがな）</label>
    </div></th>
    <td>
    <?php if('create' == $this->context->action->id): ?>

      <span class="float-box2">せい</span>
      <?= $form->field($model, 'kana01',['options'=>['class'=>'']]) ?>
      <span class="float-box2">めい</span>
      <?= $form->field($model, 'kana02',['options'=>['class'=>'']]) ?>

    <?php else: ?>

      <?= $model->kana ?>

    <?php endif ?>
    </td>
    </tr>

    <tr>
    <th><div class="required">
    <label>郵便番号</label>
    </div></th>
    <td><div class="field-signupform-zip"> <span class="float-box2">〒</span>
    <?= $form->field($model, 'zip01')->textInput(['class'=>'form-control js-zenkaku-to-hankaku Zip']) ?>
    <span class="float-box">-</span>
    <?= $form->field($model, 'zip02')->textInput(['class'=>'form-control js-zenkaku-to-hankaku Zip']) ?>
    &nbsp;
<button type="submit" class="btn btn-primary" name="scenario" value="zip2addr">住所を検索</button>
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
    <?= $form->field($model, 'pref_id')->dropDownList($prefs, ['style'=>'width:40%']) ?>
    <label class="control-label" for="signupform-addr01">市区町村名（例：千代田区神田神保町）</label>
<?php if($model->addrCandidate):
$candidate = [];
foreach($model->addrCandidate as $value)
{
    $candidate[$value] = $value;
}?>
    <?= $form->field($model, 'addr01')->dropDownList($candidate) ?>
<?php else: ?>
    <?= $form->field($model, 'addr01') ?>
<?php endif ?>

    <label class="control-label" for="signupform-addr02">番地・ビル名（例：1-3-5）</label>
    <?= $form->field($model, 'addr02') ?>
    </td>
    </tr>

    <tr>
    <th><div class="required">
    <label>電話番号</label>
    </div></th>
    <td>
    <?= $form->field($model, 'tel01', ['options'=>['class'=>'Tel']])->TextInput(['class'=>'form-control js-zenkaku-to-hankaku']) ?>
    <span class="float-box">-</span>
    <?= $form->field($model, 'tel02', ['options'=>['class'=>'Tel']])->TextInput(['class'=>'form-control js-zenkaku-to-hankaku']) ?>
    <span class="float-box">-</span>
    <?= $form->field($model, 'tel03', ['options'=>['class'=>'Tel']])->TextInput(['class'=>'form-control js-zenkaku-to-hankaku']) ?>
    </td>
    </tr>

    <tr>
    <th><div>
    <label>性別</label>
    </div></th>
    <td>
    <?= $form->field($model, 'sex_id')->dropDownList($sexes, ['style'=>'width:40%']) ?>
    </td>
    </tr>

    <tr>
    <th><div>
    <label><?= $model->getAttributeLabel('birth') ?></label>
    </div></th>
    <td>
    <?php if('create' == $this->context->action->id): ?>

      <div class="field-signupform-birth">
      <?= $form->field($model, 'birth_y',['options'=>['class'=>'Birth']])->dropDownList($years) ?>
      <span class="float-box">年</span>
      <?= $form->field($model, 'birth_m',['options'=>['class'=>'Birth']])->dropDownList($months) ?>
      <span class="float-box">月</span>
      <?= $form->field($model, 'birth_d',['options'=>['class'=>'Birth']])->dropDownList($days) ?>
      <span class="float-box">日</span>

    <?php else: ?>

    <?= $model->birth_y ?> / <?= $model->birth_m ?> / <?= $model->birth_d ?>

    <?php endif ?>

    </div></td>
    </tr>

    <tr>
    <th><div class="required">
    <label>メールアドレス</label>
    </div></th>
    <td>
    <?= $form->field($model, 'email')->input('email') ?>
    </td>
    </tr>

    <tr>
    <th><div class="required">
    <label>パスワード</label>
    </div></th>
    <td>
    <?= $form->field($model, 'password1')->passwordInput() ?>
    <p><em>確認のため、もう一度入力してください。</em></p>
    <?= $form->field($model, 'password2')->passwordInput() ?>
    </td>
    </tr>

    <tr>
    <th><div class="required">
    <label><?= $model->getAttributeLabel('subscribe') ?></label>
    </div></th>
    <td>
    <?= $form->field($model, 'subscribe')->radioList($radioList) ?>
    </td>
    </tr>

</tbody>
</table>

    <div class="form-group">

    <?= Html::submitButton("登録する", [
        'class' => 'btn btn-primary',
        'name'  => 'scenario',
        'value' => 'default',
    ]) ?>

    </div><!--form-group-->

    <?php ActiveForm::end(); ?>

  </div><!--col-md-12-->
  </div><!--row column01-->

</div><!--site-signup-->
