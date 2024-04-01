<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/signup/views/hp/update.php $
 * $Id: update.php 1373 2015-08-26 13:35:08Z mori $
 *
 * @var $this yii\web\View
 * @var $form yii\bootstrap\ActiveForm
 * @var $model \frontend\models\SignupForm
 */

$h0 = "会員登録";
$h1 = $text->member . " から移行";
$this->params['breadcrumbs'][] = ['label' => $h0, 'url' => 'index'];
$this->params['breadcrumbs'][] = $h1;
$this->params['body_id']       = 'Signup';
$this->title = sprintf('%s | %s | %s', $h1, $h0, Yii::$app->name);

// for pref_id
$prefs = \yii\helpers\ArrayHelper::map(\common\models\Pref::find()->where(['pref_id'=>range(1,47)])->all(), 'pref_id', 'name');
array_unshift($prefs, "都道府県を選択");

// for sex_id
$sexes = \yii\helpers\ArrayHelper::map(\common\models\Sex::find()->all(), 'sex_id', 'name');
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
$radioList = \yii\helpers\ArrayHelper::map(\common\models\Subscribe::find()->all(), 'subscribe_id','name');

?>

<div class="signup-update">
  <h1 class="mainTitle"><?= Html::encode($h1) ?></h1>
  <p class="mainLead">住所、電話番号などを編集できます。氏名や生年月日は登録後、ログインして修正ください</p>

<div class="regist-menu" id="sub-menu">

  <div class="row column01">
  <div class="new-regist col-md-12">
    <h2><span><?= $text->member ?> の登録情報</span></h2>

    <h5 class="list-group-item-heading">お名前</h5>
    <p class="list-group-item-text"><?= $srcCustomer->name ?> (<?= $srcCustomer->kana ?>)</p>

    <h5 class="list-group-item-heading">性別</h5>
    <p class="list-group-item-text">
      <?php
         if    ($srcCustomer->sex_id == 1) echo "男性";
         elseif($srcCustomer->sex_id == 2) echo "女性";
         else                           echo "不明";
      ?>
    </p>

    <h5 class="list-group-item-heading"><?= $model->getAttributeLabel('birth') ?></h5>
    <p class="list-group-item-text"><?= $srcCustomer->birth ? date('Y-m-d', strtotime($srcCustomer->birth)) : '(なし)'?></p>

    <h5 class="list-group-item-heading">住所</h5>
    <p class="list-group-item-text">〒<?= $srcCustomer->zip ?> <?= $srcCustomer->addr ?></p>

    <h5 class="list-group-item-heading">電話</h5>
    <p class="list-group-item-text"><?= $srcCustomer->tel ?></p>

    <h5 class="list-group-item-heading">メールアドレス</h5>
    <p class="list-group-item-text"><?= $srcCustomer->email ? $srcCustomer->email : '(なし)'?></p>
  </div><!-- new-regist -->

  <div class="change-regist col-md-12">
    <h2><span>豊受モールの登録情報</span></h2>
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
            <br><span style="color:red"> * </span>印は必須記入項目です。
        </caption>
        <tbody>

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
<a href="http://www.post.japanpost.jp/zipcode/" class="btn btn-default" target="_blank"><span class="fs12">郵便番号検索</span></a>

    &nbsp;
<button type="submit" class="btn btn-primary" name="scenario" value="zip2addr">住所を検索</button>
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
<?php if($model->addrCandidate && is_array($model->addrCandidate)):
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
      <div class="field-signupform-birth">

      <?php $inputOption = $srcCustomer->birth ? ['disabled'=>'disabled'] : []; ?>          

      <?= $form->field($model, 'birth_y',['options'=>['class'=>'Birth']])->dropDownList($years,$inputOption) ?>
      <span class="float-box">年</span>
      <?= $form->field($model, 'birth_m',['options'=>['class'=>'Birth']])->dropDownList($months,$inputOption) ?>
      <span class="float-box">月</span>
      <?= $form->field($model, 'birth_d',['options'=>['class'=>'Birth']])->dropDownList($days,$inputOption) ?>
      <span class="float-box">日</span>
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
    <?= $form->field($model, 'password1')->passwordInput(['value'=>'']) ?>
    <p><em>確認のため、もう一度入力してください。</em></p>
    <?= $form->field($model, 'password2')->passwordInput(['value'=>'']) ?>
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

  </div><!-- regist-menu -->

</div><!--signup-update-->

