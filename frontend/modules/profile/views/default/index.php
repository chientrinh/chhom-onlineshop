<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/views/default/index.php $
 * $Id: index.php 4248 2020-04-24 16:29:45Z mori $
 *
 * $model Customer model
 */
use \yii\helpers\Html;
use common\models\Membership;

$this->params['breadcrumbs'][] = ['label'=>"登録の確認"];

$jscode = "
    var target = $('.for-smart-phone').parent();
    var minWidth = 500; // iPhone 6 plus

    // 指定幅より表示幅が狭い画面の場合
    if ($(window).width() < minWidth) {
        target.hide();
    }
";
$this->registerJs($jscode);

// マイページにホメオパス活動名を表示させる判定
$homoeopath_flg = false;
foreach ($model->memberships as $mship) {
    if (in_array($mship->membership_id,[
                    Membership::PKEY_STUDENT_INTEGRATE,
                    Membership::PKEY_STUDENT_TECH_COMMUTE,
                    Membership::PKEY_STUDENT_TECH_ELECTRIC,
                    Membership::PKEY_HOMOEOPATH
                ])) {
        $homoeopath_flg = true;
        break;
    }
}
?>

<div class="cart-view">
  <h1 class="mainTitle">マイページ</h1>

      <div class="col-md-12">

    <?= \frontend\widgets\PrivilegeLinks::widget(['customer'=>$model]) ?>

  </div>

</div>
