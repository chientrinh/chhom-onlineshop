<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/contact.php $
 * $Id: contact.php 3369 2017-06-01 13:19:09Z kawai $
 *
 * @var $this yii\web\View
 * @var $form yii\bootstrap\ActiveForm
 * @var $model \frontend\models\ContactForm
 */

$title = "お問合せ";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id'] = 'Contact';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

$subjects = ["ご注文について","会員情報の修正","その他"];
?>
<div class="site-contact">
    <h1 class="mainTitle"><?= Html::encode($title) ?></h1>
    <p class="mainLead"> 豊受モールへお問い合わせの際は下記のフォームをご利用いただければ幸いです。</p>

        <div class="col-md-6">
            <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
                <?= $form->field($model, 'name') ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'subject')->dropDownList($subjects) ?>
                <?= $form->field($model, 'body')->textArea(['rows' => 8]) ?>
                <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                    'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
                ]) ?>
                <div class="form-group">
                    <?= Html::submitButton("送信", ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    
</div>
