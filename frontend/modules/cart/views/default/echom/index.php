<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/cart/views/default/index.php $
 * $Id: index.php 1853 2015-12-09 11:06:24Z mori $
 *
 * $carts array of Cart
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$title = "カートの中";
$this->title = $title;
$this->params['body_id']       = 'Cart';
$this->params['breadcrumbs'] = [];

// $this->params['breadcrumbs'][] = ['label' => "カート", 'url' => ['/cart']];
// $this->params['breadcrumbs'][] = ['label' => $title];
$this->title = sprintf('%s | %s | %s', $title, "カート", Yii::$app->name);

?>
<div class="cart-default-index">
<br />
<h1 class="mainTitle">ご注文の確認</h1>

<?php if(Yii::$app->user->isGuest && ! $customer->email): ?>
    <p class="mainLead">
        <span class="detail-view-btn">
        <?= Html::a("ログイン",['/site/login'], ['class'=>'btn btn-success']) ?>&nbsp;または&nbsp;
        <?= Html::a("登録しないで購入する",['/cart/guest/signup'], ['class'=>'btn btn-default']) ?>&nbsp;&nbsp;を選び、</span>
        ご注文の確定へお進みください。
    </p>
    <?php elseif(! Yii::$app->user->isGuest && !Yii::$app->user->identity->validate()): ?>
    <?php Yii::$app->session->addFlash('warning',
                                 '<div class="row text-center">'
                               . '<h2>お願い</h2>'
                               . "<p>お客様の会員登録が完了していません。お買い物の前に必要な情報を入力してください。</p>"
                               . \yii\helpers\Html::a('登録ページへ行く',['/profile/default/update'],['class'=>'btn btn-warning'])
                               . '</div>'
       ); ?>
<?php endif ?>


<?php if(isset($cart_idx)): ?>
<?= $this->render('_cart', ['customer'=>$customer,'model'=>$carts[$cart_idx],'key'=>$cart_idx, 'support_entry' => $support_entry])?>
<?php endif ?>

</div>
