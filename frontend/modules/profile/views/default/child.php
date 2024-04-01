<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/views/default/child.php $
 * $Id: child.php 3109 2016-11-25 04:20:50Z mori $
 *
 * $title string
 * $model CustomerChildForm
 */

use \yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->params['breadcrumbs'][] = ['label'=>$title];

$prefs = \yii\helpers\ArrayHelper::map(\common\models\Pref::find()->where(['pref_id'=>range(1,47)])->all(), 'pref_id', 'name');
array_unshift($prefs, "都道府県を選択");

$sexes = \yii\helpers\ArrayHelper::map(\common\models\Sex::find()->where(['sex_id'=>[0,1,2]])->all(), 'sex_id', 'name');
$sexes[0] = "";

?>

<div class="cart-view">
  <h1 class="mainTitle">マイページ</h1>
  <p class="mainLead">お客様ご本人のご購入履歴やお届け先の閲覧・編集などができます。</p>
  <div class="col-md-3">
	<div class="Mypage-Nav">
	  <div class="inner">
		<h3>Menu</h3>
          <?= Yii::$app->controller->nav->run() ?>
	  </div>
	</div>
  </div>

  <div class="col-md-9">
	<h2><span><?= $title ?></span></h2>
	<p class="windowtext">
      下記項目にご入力ください。「※」印は入力必須項目です。<br>
    </p>

<?php $form = ActiveForm::begin([
  'id' => 'form-create-child',
  'layout' => 'default',
    'enableClientValidation'=>false,
  'validateOnBlur'  => false,
  'validateOnChange'=> false,
  'validateOnSubmit'=> false,
  'fieldConfig'     => ['template'=>'{input}{error}'],
]);?>

<table summary="<?= $title ?>" id="FormTable" class="table table-bordered">
<tbody>

    <tr>
    <th><div class="required"><label>お名前</label></div></th>
    <td>
    <span class="float-box2">姓</span>
    <?= $form->field($model, 'name01',['options'=>['class'=>'Name']]) ?>
    <span class="float-box2">名</span>
    <?= $form->field($model, 'name02',['options'=>['class'=>'Name']]) ?>
    </td>
    </tr>

    <tr>
    <th><div class="required">
    <label>お名前（ふりがな）</label>
    </div></th>
    <td>
    <span class="float-box2">せい</span>
    <?= $form->field($model, 'kana01',['options'=>['class'=>'Name']]) ?>
    <span class="float-box2">めい</span>
    <?= $form->field($model, 'kana02',['options'=>['class'=>'Name']]) ?>
    </td>
    </tr>

    <tr>
    <th><div class="required">
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
    <td><div class="field-signupform-birth">
    <?= $form->field($model, 'birth_y',['options'=>['class'=>'Birth js-zenkaku-to-hankaku']]) ?>
    <span class="float-box">年</span>
    <?= $form->field($model, 'birth_m',['options'=>['class'=>'Birth js-zenkaku-to-hankaku']]) ?>
    <span class="float-box">月</span>
    <?= $form->field($model, 'birth_d',['options'=>['class'=>'Birth js-zenkaku-to-hankaku']]) ?>
    <span class="float-box">日</span>
    </div></td>
    </tr>

</tbody>
</table>

    <div class="form-group">

    <?php if($model->isExpired()): ?>
        <p class="alert alert-danger">
            無効になりました
        </p>
    <?php else: ?>

        <?= Html::submitButton($model->isNewRecord ? "追加する" : "更新する", [
            'class' => 'btn btn-primary',
            'name'  => 'scenario',
            'value' => 'default',
        ]) ?>
        <?php if(! $model->isNewRecord): ?>
        <?= Html::submitButton("無効にする", [
                'class' => 'btn btn-xs btn-danger pull-right',
                'name'  => 'scenario',
                'value' => 'expire',
        ]) ?>
        <?php endif ?>

    <?php endif ?>

    </div><!--form-group-->

    <?php ActiveForm::end(); ?>

  </div><!--col-md-12-->
  </div><!--row column01-->
</div>
