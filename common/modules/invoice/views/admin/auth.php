<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/invoice/views/admin/auth.php $
 * $Id: auth.php 1827 2015-11-27 08:44:44Z mori $
 *
 * @var $this  yii\web\View
 * @var $model backend\models\AuthForm
 */

use yii\helpers\Html;

$this->title = "認証";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-auth">
    <h1><?= Html::encode($this->title) ?></h1>

<p>
この機能を利用するには、専用パスワードが必要です。
</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = \yii\bootstrap\ActiveForm::begin(['id' => 'auth-form']); ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <div class="form-group">
                    <?= Html::submitButton('認証を実行', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            <?php $form->end(); ?>
        </div>
    </div>
</div>

