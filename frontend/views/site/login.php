<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/login.php $
 * $Id: login.php 3954 2018-07-04 06:08:28Z mori $
 *
 * @var $this yii\web\View
 * @var $form yii\bootstrap\ActiveForm
 * @var $model \frontend\models\LoginForm
 */

$title = "ログイン";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Login';

$crumbs = array_reverse($this->params['breadcrumbs']);
array_push($crumbs, Yii::$app->name);

$this->title = implode(' | ', $crumbs); // html>title

$ecampaign = common\models\EventCampaign::find()->active()->all();
?>
<div class="site-login">
    <h1 class="mainTitle"><?= Html::encode($title) ?></h1>

    <p class="mainLead"></p>

    <div class="row">
        <div class="coal-md-12">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= $form->field($model, 'rememberMe')->checkbox() ?>
                <?php if ($ecampaign): ?>
                    <?= $form->field($model, 'campaign_code')->textInput() ?>
                <?php endif; ?>
                <div class="form-group">
                    <?= Html::submitButton("ログイン", ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            <?php ActiveForm::end(); ?>

                <div style="color:#999;margin:1em 0">
                    パスワードを忘れた方、未設定の方はこちらから初期化できます。
                    <?= Html::a("パスワードを初期化", ['site/forgot-password'],['class'=>'btn btn-default pull-right']) ?>
                </div>
        </div>
    </div>
</div>
