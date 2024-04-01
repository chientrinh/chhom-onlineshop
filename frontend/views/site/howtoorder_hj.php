<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/howtoorder_hj.php $
 * $Id: howtoorder_hj.php 3762 2017-11-21 03:49:51Z naito $
 *
 * @var $this \yii\web\View
 */

$title = "ホメオパシージャパン販売店様用注文入力ガイド";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Howtoorder_hj';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>
<div class="site-guide">
    
    <h1>ホメオパシージャパン販売店様用注文入力ガイド</h1>
　　<p><?= Html::a('①豊受モールにログイン',['howtoorder_hj','#'=>'login'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('②マイページより、販売店様専用注文入力<br>オリジナルレメディーの購入より入力<br>一般の商品ページより商品を入力',['howtoorder_hj','#'=>'mypage'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('③商品を選択し、カートに追加',['howtoorder_hj','#'=>'select'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('④数量変更、同梱商品追加',['howtoorder_hj','#'=>'dokon'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('⑤お届け先の指定、注文の確定',['howtoorder_hj','#'=>'finish'],['class'=>'btn btn-big btn-success'])?>　
    </p>    
    <p><br><br></p>
    <h3 id="login">①豊受モールにログインします。<?= Html::a('ログイン方法はこちら',['howtologin'], ['target'=>'_blank']) ?></h3>
        <p align="center"><?= Html::img('@web/img/how_to_order_he_01.jpg') ?></p>
    <p><br><br></p>
    <h3 id="mypage">②マイページより、販売店様専用注文入力を選択</h3>
　　<h4>　　　　※一般の商品ページからのお買い物も卸売り価格となりますので、ご利用ください。 </h4>
    <h4>　　　　<?= Html::a('レメディー単品の検索方法',['howtoremedy'], ['target'=>'_blank']) ?>  
     　　　<?= Html::a('レメディーＡＢＣからの検索方法',['howtoremedyabc'], ['target'=>'_blank']) ?>
       　　<?= Html::a('レメディーキット補充の検索方法',['howtokit'], ['target'=>'_blank']) ?>  
       　　<?= Html::a('オリジナルレメディーの作成方法',['howtomk_original'], ['target'=>'_blank']) ?>  </h4>
    </li>
    <p align="center"><?= Html::img('@web/img/how_to_order_hj_01.jpg') ?></p>
    <p><br><br></p>
    <h3 id="select">③商品を選択し、カートに追加</h3>
    <p align="center"><?= Html::img('@web/img/how_to_order_hj_02.jpg') ?></p>
    <p align="center"><?= Html::img('@web/img/how_to_order_hj_03.jpg') ?></p>
    <h3>　　　＜適用書の追加方法＞</h3>    
    <p align="center"><?= Html::img('@web/img/how_to_order_hj_04.jpg') ?></p>

    <p><br><br></p>
    <h3 id="dokon">④数量変更、同梱商品追加</h3>
    <p align="center"><?= Html::img('@web/img/how_to_order_hj_05.jpg') ?></p>
    <p align="center"><?= Html::img('@web/img/how_to_order_hj_06.jpg') ?></p>
    <p><br><br></p>
    <h3 id="finish">⑤お届け先の指定と注文の確定</h3> 
    <p align="center"><?= Html::img('@web/img/how_to_order_hj_07.jpg') ?></p>
    <p><br><br></p>
    
    <h3>付録　K-Jyosoシリーズの検索方法</h3> 
    <p align="center"><?= Html::img('@web/img/how_to_order_hj_jyoso.jpg') ?></p>
    <p><br><br></p>
    <h3>付録　RX-M6の検索方法</h3> 
    <p align="center"><?= Html::img('@web/img/how_to_order_hj_rxm6.jpg') ?></p>
    <p><br><br></p>

</div>