<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/forgotPassword.php $
 * $Id: forgotPassword.php 1071 2015-06-05 06:10:38Z mori $
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

    <p>メールアドレスを入力してください。パスワード初期化ページへのリンクが送信されます。</p>

    <div class="row">
        <div class="col-md-12">
            <?php $form = ActiveForm::begin(['id' => 'forgot-password-form']); ?>
                <?= $form->field($model, 'email') ?>
                <div class="form-group">
                    <?= Html::submitButton("送信", ['class' => 'btn btn-primary']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
