<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/views/addrbook/update.php $
 * $Id: update.php 3851 2018-04-24 09:07:27Z mori $
 */

use \yii\helpers\Html;

$this->params['breadcrumbs'][] = ['label'=>"編集"];

$title = "お届け先の編集";
?>

<div class="cart-view">
  <h1 class="mainTitle">マイページ</h1>
  <p class="mainLead">お客様ご本人のご購入履歴やお届け先の閲覧・編集などができます。</p>

  <div class="col-md-3">
	<div class="Mypage-Nav">
	  <div class="inner">
		<h3>Menu</h3>
        <?= Yii::$app->controller->nav->run() ?>
	  </div>
	</div>
  </div>

  <div class="col-md-9">
	<h2><span><?= $title ?></span></h2>
	<p class="windowtext">
      下記項目にご入力ください。「※」印は入力必須項目です。<br>
    </p>

   <?php
     //   if(Yii::$app->user && (Yii::$app->user->identity->grade_id >= \common\models\CustomerGrade::PKEY_TA)) {
     //      echo $this->render('_code', ['model'=>$model]);
     //   }
   ?>
     <?= $this->render('_form', ['model'=>$model, 'candidates'=>$candidates, 'direct_flg' => $direct_flg, 'direct_customer' => $direct_customer,'title'=>$title]) ?>

  </div><!--col-md-12-->
</div><!--row column01-->
