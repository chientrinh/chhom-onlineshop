<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/guide.php $
 * $Id: guide.php 4243 2020-03-20 05:47:12Z mori $
 *
 * @var $this \yii\web\View
 */

$title = "はじめての方へ";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Guide';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>
<div class="site-guide">

    <h1 class="mainTitle"><?= Html::encode($title) ?></h1>

    <!--<p class="mainLead">楽しく簡単にお買い物いただけますように、主な手順をご案内します</p>-->

    <h2>ご視聴方法</h2>
    <p> 
	<strong></strong><br>
    ①「豊受モール」での新規会員登録、ログイン<br>
    ②参加会場「自宅受講（オンライン配信）」を選択<br>
    ③カートに入れ、、購入手続き（無料配信の場合、￥0で計算されます）<br>
    ④購入完了画面より、CHhomオンラインショップ・マイページ（右上）に進みます。ログイン後に視聴が可能です<br>
    *ご視聴環境（ブラウザ）はgoogle chromeをご利用ください<br>
    *視聴後にアンケートをご記入いただけると幸いです

    </p>

</div>
