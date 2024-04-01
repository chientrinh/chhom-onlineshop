<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/howtoorder_hp.php $
 * $Id: howtoorder_hp.php 3658 2017-10-09 11:23:55Z naito $
 *
 * @var $this \yii\web\View
 */

$title = "ホメオパシー出版書籍取扱店様用入力ガイド";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Howtoorder_hp';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>
<div class="site-guide">
    
    <h1>ホメオパシー出版書籍取扱店様用注文入力ガイド</h1>
　　<p><?= Html::a('①豊受モールにログイン',['howtoorder_hp','#'=>'login'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('②マイページより、取扱所様専用注文入力で商品を入力<br>または一般の商品ページより商品を入力',['howtoorder_hp','#'=>'mypage'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('③商品を選択し、カートに追加',['howtoorder_hp','#'=>'select'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('④数量変更、同梱商品追加',['howtoorder_hp','#'=>'dokon'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('⑤お届け先の指定、注文の確定',['howtoorder_hp','#'=>'finish'],['class'=>'btn btn-big btn-success'])?>　
    </p>    
    <p><br><br></p>
    <h3 id="login">①豊受モールにログインします。<?= Html::a('ログイン方法はこちら',['howtologin'], ['target'=>'_blank']) ?></h3>
        <p align="center"><?= Html::img('@web/img/how_to_order_he_01.jpg') ?></p>
    <p><br><br></p>
    <h3 id="mypage">②マイページより、取扱所様専用注文入力を選択　　　　<FONT COLOR=red>※一般の商品ページからのお買い物も卸売り価格となりますのでご利用ください。</FONT></h3>
    <p align="center"><?= Html::img('@web/img/how_to_order_hp_01.jpg') ?></p>
    <p><br><br></p>
    <h3 id="select">③商品を選択し、カートに追加</h3>
    <p align="center"><?= Html::img('@web/img/how_to_order_hp_02.jpg') ?></p>
    <p><br><br></p>
    <h3 id="dokon">④数量変更、同梱商品追加</h3>
    <p align="center"><?= Html::img('@web/img/how_to_order_hp_03.jpg') ?></p>
    <p><br><br></p>
    <h3 id="finish"><h3>⑤お届け先の指定と注文の確定</h3> 
    <p align="center"><?= Html::img('@web/img/how_to_order_hp_04.jpg') ?></p>
    <p><br><br></p>

</div>