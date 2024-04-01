<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/howtoby_tekiyosyo.php $
 * $Id: howtoby_tekiyosyo.php 3570 2017-08-26 02:40:09Z naito $
 *
 * @var $this \yii\web\View
 */

$title = "処方された適用書の購入方法";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Howtoby_tekiyosyo';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>
<div class="site-guide">

    <h3>適処方された適用書の購入方法</h3>
    
    <p><font color=#990066"> ※豊受モールでは、豊受モールで作成された適用書のみ購入することができます。<br>
        　ご不便をお掛けしますが、従来のサイトで作成された適用書は、従来のサイトもしくはショップでのご購入をお願い致します。</font><br><br> </p>
    <p>トップ画面の【適用書レメディーの購入】をクリックしてください。</p>
    <p><?= Html::img('@web/img/how_to_by_ty_01.jpg') ?></p>
    <p><br></p>
    <p>レメディー・ハーブ酒カテゴリーを開いて、左下にある【適用書レメディーの購入】をクリックしても、購入できます。</p>
    <p><?= Html::img('@web/img/how_to_by_ty_02.jpg') ?></p>
    <p><?= Html::img('@web/img/how_to_by_ty_03.jpg') ?></p>
    <p><br></p>
    <p>スマートフォンの場合は、右上の黒いメニューをタップすると、【適用書レメディーの購入】がプルダウンされます。</p>
    <p>または、レメディー・ハーブ酒カテゴリーを開いて、検索タブをタップすると【適用書レメディーの購入】が表示されます。</p>
    <p><?= Html::img('@web/img/how_to_by_ty_04.jpg') ?></p>
    <p><br></p>
    <p>適用書番号とパスワードを入力して【検索】ボタンをクリックすると、適用書が表示されます。</p>
    <p><?= Html::img('@web/img/how_to_by_ty_05.jpg') ?></p>
    <p><br></p>
    <p>【購入】ボタンをクリックすると、お買い物かご（カート）に入ります。</p>
    <p><?= Html::img('@web/img/how_to_by_ty_06.jpg') ?></p>
    <p><br></p>
</div>