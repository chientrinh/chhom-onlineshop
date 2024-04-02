<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/signup/views/default/create.php $
 * $Id: create.php 1117 2015-06-30 16:31:16Z mori $
 *
 * @var $this yii\web\View
 * @var $form yii\bootstrap\ActiveForm
 * @var $model \frontend\models\SignupForm
 */

$h0 = "会員登録";
$h1 = "新規登録";
$this->params['breadcrumbs'][] = ['label' => $h0, 'url' => 'index'];
$this->params['breadcrumbs'][] = $h1;
$this->params['body_id']       = 'Signup';
$this->title = sprintf('%s | %s | %s', $h1, $h0, Yii::$app->name);
?>

<div class="signup-create">
    <h1 class="mainTitle"><?= Html::encode($h1) ?></h1>
    <p class="mainLead">新しく豊受モール会員になる方は、以下にご記入をおねがいします。</p>

             <?= $this->render('_form', ['model'=>$model]) ?>

</div><!--signup-create-->
