<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/howtoremedy.php $
 * $Id: howtoremedy.php 3492 2017-07-11 03:20:33Z naito $
 *
 * @var $this \yii\web\View
 */

$title = "商品の選び方（レメディー単品）";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Howtoremedy';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>
<div class="site-guide">

    <h3>商品の選び方（レメディー単品）</h3>
    <p>【レメディー・ハーブ酒】をクリックします。</p>
    <p align="center"><?= Html::img('@web/img/how_to_remedy_01.jpg') ?></p>
    <p><br></p>
    <p>左側の【単品レメディー】をクリックします。</p>
    <p align="center"><?= Html::img('@web/img/how_to_remedy_02.jpg') ?></p>
    <p><br></p>
    <p>例として、Aconを検索してみましょう。</p>
    <p align="center"><?= Html::img('@web/img/how_to_remedy_03.jpg') ?></p>
    <p><br></p>
    <p>【Acon】と入力します。容器とポーテンシーを選択し、【検索】ボタンをクリックします。</p>
    <p align="center"><?= Html::img('@web/img/how_to_remedy_04.jpg') ?></p>
    <p><br></p>
    <p>LMポーテンシーを検索することもできます。（LMポーテンシーは、ログインした場合のみ表示されます。） </p>
    <p align="center"><?= Html::img('@web/img/how_to_remedy_05.jpg') ?></p>
    <p><br></p>
    <p>【カートに入れる】ボタンをクリックすると、カートに入ります。</p>
    <p align="center"><?= Html::img('@web/img/how_to_remedy_06.jpg') ?></p>
    <p><br></p>
    <p>【カートを見る】をクリックすると、カートに進みます。</p>
    <p align="center"><?= Html::img('@web/img/how_to_remedy_07.jpg') ?></p>
    <p><br></p>
    <p>お買い物を続けたい場合は、【お買い物を続ける】ボタンをクリックして下さい。<br>
       ご注文の確定は、<?= Html::a('お買い物の確定方法',['howtobuy'], ['target'=>'_blank']) ?>をご覧下さい。</p>
    <p align="center"><?= Html::img('@web/img/how_to_remedy_08.jpg') ?></p>
    <p><br></p>
</div>