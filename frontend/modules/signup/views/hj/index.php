<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/signup/views/default/index.php $
 * $Id: index.php 1387 2015-08-28 02:38:34Z mori $
 *
 * @var $this \yii\web\View
 */

$title = "会員登録";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Signup';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>

<div class="signup-index">
    <h1 class="mainTitle"><?= Html::encode($title) ?></h1>

    <p class="mainLead">ホメオパシージャパン自然の会からの会員情報の豊受モールへの移行は、９月３０日以降に可能となります。
今しばらくお待ち下さい。
        なお、自然の会から移行すると豊受モールの「スタンダード」会員となります。</p>
    <p class="mainLead">
        ※豊受モールのオープンは１２月に延期となりました。何卒ご了承くださいますようお願いいたします。
    </p>
    <br>

    <div id="img-banner" class="text-center">
        <img src="/img/under_construction.jpg" alt="ただいま工事中です" style="max-width:400px">
    </div>
</div>

