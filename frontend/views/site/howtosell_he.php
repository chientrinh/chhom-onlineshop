<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/howtosell_he.php $
 * $Id: howtosell_he.php 4067 2018-11-28 08:10:14Z kawai $
 *
 * @var $this \yii\web\View
 */

$title = "豊受オーガニクスショップ提携取扱所様用売上入力ガイド";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Howtoorder_he';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>
<div class="site-guide">
    
    <h1>豊受オーガニクスショップ提携取扱所様用売上入力ガイド</h1>
    
    <p>商品とお客様を⼊⼒し、売上を立てることで、お客様のランクに応じた豊受モールポイントが⾃動計算され、付与されます。</p>
    <p>お⽀払時に 豊受モールポイントを使っていただくことができます。</p>
    
　　<p><?= Html::a('①豊受モールにログイン',['howtosell_he','#'=>'login'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('②マイページより、取扱所様専用売上入力へ',['howtosell_he','#'=>'mypage'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('③起票するをクリック',['howtosell_he','#'=>'kihyo'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('④商品を入力',['howtosell_he','#'=>'syohin'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('⑤お客様を入力',['howtosell_he','#'=>'customer'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('⑥お預かりを入力',['howtosell_he','#'=>'money'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('⑦確定',['howtosell_he','#'=>'finish'],['class'=>'btn btn-big btn-success'])?>　
    </p>    
    <p><br><br></p>
    <h3 id="login">①豊受モールにログインします。<?= Html::a('ログイン方法はこちら',['howtologin'], ['target'=>'_blank']) ?></h3>
        <p align="center"><?= Html::img('@web/img/how_to_order_he_01.jpg') ?></p>
    <p><br><br></p>
    <h3 id="mypage">②マイページより、取扱所様専用売上入力を選択 </h3>
    <p align="center"><?= Html::img('@web/img/how_to_sell_he_01.jpg') ?></p>
    <p><br><br></p>
    <h3 id="kihyo">③起票するをクリック</h3>
    <p align="center"><?= Html::img('@web/img/how_to_sell_he_02.jpg') ?></p>
    <h3 id="syohin">④商品を入力</h3> 
    <p>※商品バーコードを読み取ることで、商品が選択可能です。バーコードリーダーを使⽤することで、業務の効率化を図ることができます。 </p>
    <p align="center"><?= Html::img('@web/img/how_to_sell_he_03.jpg') ?></p>
    <p align="center"><?= Html::img('@web/img/how_to_sell_he_04.jpg') ?></p>
    <h3 id="customer">⑤お客様を入力</h3>
    <p>お客様に「豊受オーガニクスモール会員証」を提示していただくと、会員証バーコードで特定が可能です。</p>
    <p>お客様の電話番号でも検索ができます。</p>
    <p align="center"><?= Html::img('@web/img/how_to_sell_he_05.jpg') ?></p>
    <p align="center"><?= Html::img('@web/img/how_to_sell_he_06.jpg') ?></p>
    <p align="center"><?= Html::img('@web/img/how_to_sell_he_07.jpg') ?></p>
    <p><br><br></p>
    <h3 id="money">⑥お預かりを入力</h3>
    <p>お⽀払時に 豊受モールポイントを使っていただくことができます。</p>
    <p align="center"><?= Html::img('@web/img/how_to_sell_he_08.jpg') ?></p>
    <p><br><br></p>
    <h3 id="finish">⑦確定</h3> 
    <p>確定した時点で、お客様のランクに応じた豊受モールポイントが⾃動計算され、付与されます。</p>
    <p align="center"><?= Html::img('@web/img/how_to_sell_he_09.jpg') ?></p>
    
    <p>元の画面に戻るには、「次のお買い物」をクリックします。「印刷する」でレシート印刷ができます。<br>
        レシートプリンターを導入していただくと、レシート用紙に印刷ができます。</p>
    <p align="center"><?= Html::img('@web/img/how_to_sell_he_10.jpg') ?></p>
    <p>履歴を見る場合は、履歴をクリックします。</p>
    <p align="center"><?= Html::img('@web/img/how_to_sell_he_11.jpg') ?></p>
    <p>売上伝票を修正、キャンセルする場合は、伝票NOをクリックします。</p>
    <p align="center"><?= Html::img('@web/img/how_to_sell_he_12.jpg') ?></p>
    <p><br><br></p>
    
    <h3>※付与ポイント、使用ポイントの精算について</h3> 
    <h4>・付与ポイント</h4> 
    <p>　　お客様に付与したポイントは、「豊受オーガニクスモールポイント付与料」（1ポイント=1円）として、請求させていただきます。<br>
　　ただし、販売⾦額に対して5%分のポイントは 豊受オーガニクスモールより補填いたします。<br>
　　販売⾦額5%以上のポイントをお客様に付与された場合は、5%分を差し引いた形でのご請求となります。<br><br>
　　例）<br>
　　スペシャル会員（5%付与）のお客様が、10,000円分の商品を購入された場合、お客様には、5%分の 500ポイントが付与されます。<br>
　　5%分のポイントは、豊受オーガニクスモールで補填しますので、ポイント付与に関する請求はいたしません<br><br>
  
　　プレミアムプラス会員（20%付与）のお客様が、10,000円分の商品を購入された場合、お客様には、20%分の 2,000ポイントが付与されます。<br>
　　5%分のポイントは、豊受オーガニクスモールで補填しますので、15%分のポイントにあたる 1,500円を請求させていただきます。<br></p>
    
    <h4>・ポイント使用</h4> 
    <p>　　お客様が商品購⼊時にポイント使⽤（値引）された場合は、使⽤したポイント（1ポイント=1円）分は、<br>
　　請求時に、使⽤ポイント分の⾦額を全額、相殺という形で補填させていただきます。</p>



</div>