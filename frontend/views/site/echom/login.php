<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

#include("/var/www/mall/frontend/web/set_mall_num.php");
include("/var/www/mall/frontend/web/expire_data.php");

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
header('Set-Cookie: cross-site-cookie=name; SameSite=None; Secure');
$csscode = "
#loading-view {
 /* 領域の位置やサイズに関する設定 */
 width: 100%;
 height: 100%;
 z-index: 9999;
 position: fixed;
 top: 0;
 left: 0;
 /* 背景関連の設定 */
 background-color: #000000;
 filter: alpha(opacity=85);
 -moz-opacity: 0.85;
 -khtml-opacity: 0.85;
 opacity: 0.85;
 background-image: url(/img/loading.gif);
 background-position: center center;
 background-repeat: no-repeat;
 background-attachment: fixed;
}
#text {
  width: 100%;
  height: 100%;
  color:#FFFFFF;
  font-size:14px;
  font-weight:bold;
  padding-top : 100px;
  /* Firefox */
  display: -moz-box;
  -moz-box-pack: center;
  -moz-box-align: center;
  /* Safari and Chrome */
  display: -webkit-box;
  -webkit-box-pack: center;
  -webkit-box-align: center; 
  /* W3C */
  display: box;
  box-pack: center;
  box-align: center;
}
";
$this->registerCss($csscode);

$jscode = "
$('button[name=login-button]').click(function() {
    console.log('hello');
    email = $('#loginform-email').val();
    password = $('#loginform-password').val();
    rememberme =   $('#loginform-rememberme').val();
    console.log(email,password,rememberme);

    if(email.length==0) {
        console.log('email empty');
    }

    if(password.length==0){
        console.log('password empty');
    }

    var form = $('#login-form');
    var data = form.serialize();

    $.ajax({
        url: 'https://stream.homoeopathy.ac/must_email.php',
        type: 'GET',
        data: { \"param1\": $('#loginform-email').val() , \"param2\": $('#loginform-password').val() , \"server_num_expire\": $server_num_expire },
        dataType: 'json',
        xhrFields: {
            withCredentials: true
        },
        timeout: 30000,
        success: function (data) {
            // 成功したときの実装
            console.log(data);    
            if(data.code == 1) {
                //redirect the page to url.
                // window.location.href = data.url;      
                console.log('submit OK');
                form.submit();
                return true;   
            } else {
                loadingView(false);
                $('a').attr('disabled', false);
                $('#loginform-password').next().text('IDまたはパスワードが異なっています');
                $('#loginform-password').parent().addClass('has-error').removeClass('has-success');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            form.submit();
            return true;   
        }
    });

});

function loadingView(flag) {
    $('#loading-view').remove();
    if(!flag) return;
    $('<div><div id=\'loading-view\'><div id=\'text\'>ログイン処理中です。しばらくお待ち下さい...</div></div></div>').appendTo('body');
}
";
$this->registerJs($jscode);
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
                    <?= Html::Button("ログイン", ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            <?php ActiveForm::end(); ?>

                <div style="color:#999;margin:1em 0">
                    パスワードを忘れた方、未設定の方はこちらから初期化できます。
                    <?= Html::a("パスワードを初期化", 'https://mall.toyouke.com/index.php/site/forgot-password',['class'=>'btn btn-default pull-right']) ?>
                </div>
        </div>
    </div>
</div>
