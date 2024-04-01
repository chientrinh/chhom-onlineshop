<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/howtoorder_he.php $
 * $Id: howtoorder_he.php 4067 2018-11-28 08:10:14Z kawai $
 *
 * @var $this \yii\web\View
 */

$title = "豊受オーガニクスショップ提携取扱所様用注文入力ガイド";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Howtoorder_he';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>
<div class="site-guide">
    
    <h1>豊受オーガニクスショップ提携取扱所様用注文入力ガイド</h1>
　　<p><?= Html::a('①豊受モールにログイン',['howtoorder_he','#'=>'login'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('②マイページより、取扱所様専用注文入力で商品を入力<br>または一般の商品ページより商品を入力',['howtoorder_he','#'=>'mypage'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('③商品を選択し、カートに追加',['howtoorder_he','#'=>'select'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('④数量変更、同梱商品追加',['howtoorder_he','#'=>'dokon'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('⑤お届け先の指定、注文の確定',['howtoorder_he','#'=>'finish'],['class'=>'btn btn-big btn-success'])?>　
    </p>    
    <p><br><br></p>
    <h3 id="login">①豊受モールにログインします。<?= Html::a('ログイン方法はこちら',['howtologin'], ['target'=>'_blank']) ?></h3>
        <p align="center"><?= Html::img('@web/img/how_to_order_he_01.jpg') ?></p>
    <p><br><br></p>
    <h3 id="mypage">②マイページより、取扱所様専用注文入力を選択  　<?= Html::a('※一般の商品ページからのお買い物も卸売り価格となります。追加方法はこちら',['howtoselect'], ['target'=>'_blank']) ?></h3>
    <p align="center"><?= Html::img('@web/img/how_to_order_he_02.jpg') ?></p>
    <p><br><br></p>
    <h3 id="select">③商品を選択し、カートに追加</h3>
    <p align="center"><?= Html::img('@web/img/how_to_order_he_03.jpg') ?></p>
    <p align="center"><?= Html::img('@web/img/how_to_order_he_04.jpg') ?></p>
    <p align="center"><?= Html::img('@web/img/how_to_order_he_05.jpg') ?></p>
    <p><br><br></p>
    <h3 id="dokon">④数量変更、同梱商品追加</h3>
    <p align="center"><?= Html::img('@web/img/how_to_order_he_06.jpg') ?></p>
    <p><br><br></p>
    <h3 id="finish"><h3>⑤お届け先の指定と注文の確定</h3> 
    <p align="center"><?= Html::img('@web/img/how_to_order_he_07.jpg') ?></p>
    <p><br><br></p>

</div>