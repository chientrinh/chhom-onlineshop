<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/signup/views/default/search.php $
 * $Id: search.php 1540 2015-09-22 15:41:07Z mori $
 *
 * @var $this yii\web\View
 * @var $company \common\models\Company
 * @var $model \frontend\models\SignupForm
 */

$h0 = "会員登録";
$h1 = "会員検索";
$this->params['breadcrumbs'][] = ['label' => $h0, 'url' => 'index'];
$this->params['breadcrumbs'][] = $h1;
$this->params['body_id']       = 'Signup';
$this->title = sprintf('%s | %s | %s', $h1, $h0, Yii::$app->name);

$jscode = "
  $(function() {
    if(!$('.agreed').checked)
      $('.user-input').attr('disabled',true);
  });

  $('.agreed').click(function(){
      if(this.checked)
         $('.user-input').removeAttr('disabled');
      else
         $('.user-input').attr('disabled',true);
  	return true;
  });

";
$this->registerJs($jscode);

?>

<div class="signup-search">

    <h1 class="mainTitle"><?= Html::encode($h1) ?></h1>

    <p class="mainLead">
    「<span><strong><?= $text->member ?>から移行</strong></span>」が選択されました。<br>
    豊受モールは、<?= $company->name ?> からお客様の個人情報を受け取ります。<br>
        <?php if('hj' == $this->context->id): ?>
        <strong><?= Html::a("利用規約",['/site/usage']) ?></strong> と
        <?php endif ?>
    <strong><?= Html::a("プライバシーポリシー",['/site/policy']) ?></strong> をご確認・同意の上お進みください。
</p>
<div class="form-group field-customersearchform-agreed">
	  <div class="checkbox">
		<label for="customersearchform-agreed">
		  <input type="hidden" name="CustomerFinder[agreed]" value="0">
		  <input type="checkbox" id="customersearchform-agreed" class="agreed " name="CustomerSearchForm[agreed]" value="<?= $model->agreed ?>">
        <?php if('hj' == $this->context->id): ?>
		  <strong>利用規約とプライバシーポリシーに同意する</strong></label>
        <?php else: ?>
		  <strong>プライバシーポリシーに同意する</strong></label>
        <?php endif ?>
		<p class="help-block help-block-error"></p>
	  </div>
	</div>

	<div class="sub-menu">
	  <div class="row">
		<div class="col-md-12">

           <?php $form = ActiveForm::begin([
               'id'       => 'form-signup',
           ]); ?>

			<p><span><?= $text->message ?>。</span></p>

            <?php if(('he' == $this->context->id) && (null == Yii::$app->request->get('target'))): ?>
                <label>新会員番号</label>
			    <?= $form->field($model, 'userid',  ['template'=>'{input}{hint}{error}'])->textInput(['class'=>'user-input form-control']) ?>
                <label>仮パスワード</label>
                <?= $form->field($model, 'password',['template'=>'{input}{hint}{error}'])->passwordInput(['value'=>'','class'=>'user-input form-control']) ?>
            <?php else: ?>
			    <?= $form->field($model, 'userid')->textInput(['class'=>'user-input form-control']) ?>
                <?= $form->field($model, 'password')->passwordInput(['value'=>'','class'=>'user-input form-control']) ?>
            <?php endif ?>

            <?= Html::activeHiddenInput($model, 'agreed',['value'=>1]) ?>

            <?php if(Yii::$app->request->get('target')): ?>
              <?= Html::activeHiddenInput($model, 'target',[
                  'value' => Yii::$app->request->get('target'),
              ]) ?>
            <?php endif ?>
			
              <?= Html::submitButton("会員検索", [
                  'class' => 'btn btn-primary user-input',
                  'name'  => 'scenario',
                  'value' => 'default',
              ]) ?>

            <?php ActiveForm::end(); ?>

		</div>
	  </div>
	</div><!-- sub-menu -->

    <!-- hide tieup-info
	<div class="tieup-info">
	  <h4>提携企業および会員情報</h4>
	  <ul>
		<li>ホメオパシックエデュケーション（株）：ホメオパシーとらのこ会員、CHhom学生情報</li>
		<li>ホメオパシー出版（株）：オンラインショップ会員</li>
		<li>ホメオパシー・ジャパン（株）：自然の会会員</li>
		<li>JPHMA：認定会員</li>
	  </ul>
	</div>
    -->

</div><!--signup-search-->
