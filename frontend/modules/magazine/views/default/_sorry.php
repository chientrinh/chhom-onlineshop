<?php

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/magazine/views/default/_sorry.php $
 * $Id: _sorry.php 1287 2015-08-13 08:52:38Z mori $
 *
 *
 * $this app\View
 */

use \yii\helpers\Html;

// instead of bundle Bootstrap, register css as portion 2015.07.31 mori

$csscode = "
img {
    opacity: 0.4;
    filter: alpha(opacity=40); /* For IE8 and earlier */
}
.text-center {
  text-align: center;
}
.alert-danger {
  color: #a94442;
  background-color: #f2dede;
  border-color: #ebccd1;
}
.alert {
  padding: 15px;
  margin-bottom: 20px;
  border: 1px solid transparent;
  border-radius: 4px;
}
.btn-success {
  color: #fff;
  background-color: #5cb85c;
  border-color: #4cae4c;
}
.btn {
  display: inline-block;
  padding: 6px 12px;
  margin-bottom: 0;
  font-size: 14px;
  font-weight: normal;
  line-height: 1.42857143;
  text-align: center;
  white-space: nowrap;
  vertical-align: middle;
  -ms-touch-action: manipulation;
      touch-action: manipulation;
  cursor: pointer;
  -webkit-user-select: none;
     -moz-user-select: none;
      -ms-user-select: none;
          user-select: none;
  background-image: none;
  border: 1px solid transparent;
  border-radius: 4px;
}";
$this->registerCss($csscode);

$route = array_merge(['/magazine/login/view'],  Yii::$app->request->queryParams);

?>
<div class="alert alert-danger">
    こちらのコンテンツは会員限定です。会員登録いただけますと続きをお読みいただけます。
    <p>&nbsp;</p>
    <p class="text-center" style="width:50%;float:left">
        <?= Html::a("ログイン", $route, ['class'=>'btn btn-success']) ?>
        <small>会員の方はこちら</small>
    </p>

    <p class="text-center">
        <?= Html::a("会員登録", ['/signup'],['class'=>'btn btn-success']) ?>
        <small>今すぐ会員登録する</small>
    </p>

    <p>&nbsp;</p>
    <p>
        あるいは、会員登録の前に <?= Html::a("デモ版のコンテンツ",['/magazine/default/home','demo'=>'true'],['class'=>'btn']) ?> をお読みいただけます。
    </p>

</div>
