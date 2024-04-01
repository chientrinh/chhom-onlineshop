<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/forgotPassword_thankyou.php $
 * $Id: forgotPassword_thankyou.php 1099 2015-06-23 07:50:57Z mori $
 *
 * @var $this yii\web\View
 * @var $form yii\bootstrap\ActiveForm
 * @var $model \frontend\models\PasswordResetRequestForm
 */

$title = "パスワードを初期化する";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'PassReset';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>
<div class="site-forgot-password">

    <h1 class="mainTitle"><?= Html::encode($title) ?></h1>

    <p>ご指定のメールアドレスへメールを送信しました。記載の手順にしたがってお進みください。</p>

    <div class="row">
        <div class="col-md-12">
            <?php $form = ActiveForm::begin(['id' => 'forgot-password-form']); ?>
             <?= $form->field($model, 'email')->textInput(['disabled'=>'disabled']) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
