<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/views/history/email.php $
 * $Id: email.php 3109 2016-11-25 04:20:50Z mori $
 *
 * $model
 */

use \yii\helpers\Html;

$this->params['breadcrumbs'][] = ['label' => "メール履歴"];

$csscode = "
.detail-view th {
  width: 10%;
}
";
$this->registerCss($csscode);
?>

<div class="profile-history-index">

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
	<h3><span class="mypage-history">メール履歴</span></h3>
<?= \yii\widgets\DetailView::widget([
    'model'  => $model,
    'attributes' => [
        'subject',
        'to',
        [
            'attribute' => 'date',
            'format' => 'html',
        ],
        'body:ntext',
    ],
]) ?>

    </div><!-- col-md-9 -->

</div>
