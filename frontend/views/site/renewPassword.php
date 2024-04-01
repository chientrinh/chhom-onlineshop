<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/renewPassword.php $
 * $Id: renewPassword.php 1071 2015-06-05 06:10:38Z mori $
 *
 * @var $this yii\web\View
 * @var $form yii\bootstrap\ActiveForm
 * @var $model \frontend\models\ResetPasswordForm
 */

$title = "パスワードを初期化する";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'PassReset';

$crumbs = array_reverse($this->params['breadcrumbs']);
array_push($crumbs, Yii::$app->name);

$this->title = implode(' | ', $crumbs); // html>title
?>
<div class="site-renew-password">

    <h1 class="mainTitle"><?= Html::encode($title) ?></h1>
    <p class="mainLead">新しいパスワードを入力してください。</p>

    <div class="row">
        <div class="col-md-12">
            <?php $form = ActiveForm::begin(['id' => 'renew-password-form']); ?>
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
