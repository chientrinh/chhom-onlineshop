<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/site/forgotPassword.php $
 * $Id: forgotPassword.php 1844 2015-12-04 07:37:29Z mori $
 *
 * @var $this yii\web\View
 * @var $form yii\bootstrap\ActiveForm
 * @var $model \frontend\models\PasswordResetRequestForm
 */

$this->title = "パスワードを初期化する";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-forgot-password">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>メールアドレスを入力してください。パスワード初期化ページへのリンクが送信されます。</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'forgot-password-form']); ?>
                <?= $form->field($model, 'email') ?>
                <div class="form-group">
                    <?= Html::submitButton("送信", ['class' => 'btn btn-primary']) ?>
                </div>
            <?php ActiveForm::end(); ?>
                <?php if($model->hasErrors('email')): ?>
                    <p class="alert alert-warning">
                        メールアドレスに入力間違いがないか今いちど確認の上、当社システム部まで「メールアドレスの登録」を依頼してください。
                    </p>
                <?php endif ?>
        </div>
    </div>
<?php
$error = Yii::$app->session->getFlash('error');
if($error)
    echo '<div class="alert alert-danger">', $error, '</div>';
?>
</div>
