<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/site/renewPassword.php $
 * $Id: renewPassword.php 1773 2015-11-06 05:25:58Z mori $
 *
 * @var $this yii\web\View
 * @var $form yii\bootstrap\ActiveForm
 * @var $model \frontend\models\ResetPasswordForm
 */

$this->title = "パスワードを初期化する";
$this->params['breadcrumbs'][] = $this->title;

$success = Yii::$app->session->getFlash('success');
if($success)
{
    echo Html::a("ログインする", \yii\helpers\Url::toRoute('site/login'),['class'=>'btn btn-primary']);
    return;
}
?>
<div class="site-renew-password">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>新しいパスワードを入力してください。</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'renew-password-form']); ?>
                <?= $form->field($model, 'email'   )->textInput() ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= $form->field($model, 'retype'  )->passwordInput() ?>
                <div class="form-group">
                    <?= Html::submitButton("保存", ['class' => 'btn btn-primary']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
