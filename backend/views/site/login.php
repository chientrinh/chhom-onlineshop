<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/site/login.php $
 * $Id: login.php 1827 2015-11-27 08:44:44Z mori $
 *
 * @var $this  yii\web\View
 * @var $model common\models\LoginForm
 */

use yii\helpers\Html;

$this->title = "ログイン";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

<p>
かならず本人のメールアドレスでログインしてください。
ログインしたメールアドレスに基づいて操作履歴が記録されます。
</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = \yii\bootstrap\ActiveForm::begin(['id' => 'login-form']); ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= $form->field($model, 'rememberMe')->checkbox() ?>
                <div class="form-group">
                    <?= Html::submitButton('ログイン', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            <?php $form->end(); ?>
                <div style="color:#999;margin:1em 0">
                    メールアドレスを持つ当グループ各社の従業員は全員ログインできます。
                    パスワード未登録（初めてログインする場合）またはパスワードを忘れた場合はこちらで初期化の手続きをしてください。
                    <?= Html::a("初期化する", ['site/forgot-password'],['class'=>'btn btn-default']) ?>
                </div>
        </div>
    </div>
</div>
