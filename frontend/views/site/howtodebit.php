<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/howtodebit.php $
 * $Id: howtodebit.php 4243 2020-03-20 05:47:12Z mori $
 *
 * @var $this \yii\web\View
 */

$title = "口座振替の登録方法";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Howtodebit';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>
<div class="site-guide">
    <p><font color=#990066">※お申し込みできる銀行口座は、個人の口座に限られています。法人や団体の口座はお申し込みできませんので、ご了承願います。</font></p>
    <h2>口座振替にご登録頂くと・・・</h2>
    <ul>
      <li>ご登録の確認ができるのは、ご登録されてから２～５営業日後となっております。それまでは、代引でのご購入となります。</li>
      <li>ご登録の確認ができますと、カートのお支払い方法に口座振替が設定されます。代引に変更することも可能です。</li>
      <li>口座振替でご購入された金額は、月末締めで合算し、翌月26日付で自動引落しとなります。</li>
      <li>自動引落しができなかった場合、豊受モールのご利用を制限させていただくことがあります。</li>
      <li>トミーローズの商品には、口座振替をご利用いただけません。</li>
      <li>ご本人登録住所以外へのお届けには、納品書に金額を記載しないサービスをご利用いただけます。</li>
    </ul>
    <p><br></p>
    <h2>登録のながれ</h2>
　　<p>　　<?= Html::a('１.登録 &#10148 メール送信',['howtodebit','#'=>'touroku'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('２.メールのurlよりログイン',['howtodebit','#'=>'login'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('３.金融機関のサイトで手続き',['howtodebit','#'=>'bank'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('４.登録完了',['howtodebit','#'=>'finish'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('５.ご登録の確認',['howtodebit','#'=>'response'],['class'=>'btn btn-big btn-success'])?>　<font size = 4 color=#008000>&#10148; </font>
       <?= Html::a('６.口座振替開始',['howtodebit','#'=>'start'],['class'=>'btn btn-big btn-success'])?></p>
    <p><br></p>
    <ol>
      <li>登録 &#10148 メール送信　　　　：登録ボタンをクリックすると、２通のメールが送信されます。（メールは２４時間有効です。）</li>
      <li>メールのurlよりログイン　：１通めのメールのＵＲＬをクリックして、ログイン画面に進みます。<br>
          　　　　　　　　　　　　　１通めのメールのお客様番号と、２通めのメールのパスワードを入力してログインします。（ログインしてから１５分間有効です。）</li>
      <li>金融機関のサイトで手続き：金融機関を指定し、【金融機関へ】をクリックすると、各金融機関のページに進みます。</li>
      <li>登録完了　　　　　　　　：各金融機関のページで、手続きが完了すると、「口座振替申込み受付完了のお知らせ」というメールが送信されます。<br>
          　　　　　　　　　　　　　<font color="#990066">お客様の作業はこれで完了です。このまま登録の確認が終わるまでお待ちください。<br>
          　　　　　　　　　　　　　※各金融機関のご登録は、１日１回までとなっております。口座を変更されたい場合は、翌日以降改めて行ってください。</font></li>
      <li>ご登録の確認　　　　　　：豊受モールにて、ご登録の確認ができるのは、ご登録されてから２～５営業日後となっております。<br>
          　　　　　　　　　　　　　ご登録の確認ができますと、マイページの表示が「口座の登録は以下の日時をもって完了しました」に変更されます。</li>
      <li>口座振替開始　　　　　　：豊受モールにてご登録の確認ができますと、口座振替でお買い物ができるようになります。<br>
          　　　　　　　　　　　　　カートのお支払い方法に口座振替が設定されます。代引に変更することも可能です。</li>
    </ol>
    <p><br></p>
    <h2>口座振替の登録方法</h2>  
    <p><br></p>
    <p>ログイン後、【マイページ】をクリックし、マイページ画面に進みます。　　<font color="#990066">登録される前に、口座番号や通帳などを手元にご用意ください。</font></p>
     <p align="center"><?= Html::img('@web/img/how_to_tekiyousyo_01.jpg') ?></p>
    <p><br></p>
    <p>【口座振替】をクリックすると、口座振替の画面に進みます。</p>
    <p align="center"><?= Html::img('@web/img/how_to_debit_01.jpg') ?></p>
    <p><br></p>
    
    <h3 id="touroku">１.登録→メール送信</h3>  
    <p >【登録】ボタンをクリックすると、登録頂いているメールアドレスに、<font color="#990066"><strong>２通</strong>のメールが送信されます。（メールは２４時間有効です。）</font></p>
    <p align="center"><?= Html::img('@web/img/how_to_debit_02.jpg') ?></p>
    <p><br></p>
    <p>メールが届かない場合は、 info@nekonet.co.jp からのメールが受信できるようにして、もう一度お試しください。<br>
    <p align="center"><?= Html::img('@web/img/how_to_debit_03.jpg') ?></p>
    <p><br></p>    
    <p>１通めのメールには、①口座振替登録のログイン画面のＵＲＬと、②お客様番号が記されています。<br>
    <p align="center"><?= Html::img('@web/img/how_to_debit_04.jpg') ?></p>
    <p><br></p>   
    <p>２通めのメールには、③パスワードが記されています。<br>
    <p align="center"><?= Html::img('@web/img/how_to_debit_05.jpg') ?></p>
    <p><br></p>  
    
    <h3 id="login">２.メールのUrlよりログイン</h3>  
    <p>メール１通めの、①口座振替登録のログイン画面のＵＲＬをクリックします。<br>
       ※ただしログイン後１５分以内に手続きを完了させる必要がありますので、<ins>口座番号や通帳などを手元にご用意ください。</ins></p> 
    <p align="center"><?= Html::img('@web/img/how_to_debit_06.jpg') ?></p>
    <p><br></p>   
    <p>内容を確認して、【次へ】をクリックします。<br>
    <p align="center"><?= Html::img('@web/img/how_to_debit_065.jpg') ?></p>
    <p><br></p> 
    <p>ご登録される金融機関をクリックします。<br>
    <p align="center"><?= Html::img('@web/img/how_to_debit_07.jpg') ?></p>
    <p><br></p>   
    <p>店番号、預金種別、口座番号、口座名義人（全角カタカナ）を入力し、【次へ】をクリックします。<br>
    <p align="center"><?= Html::img('@web/img/how_to_debit_08.jpg') ?></p>
    <p><br></p>   
    
    <h3 id="bank"> ３.金融機関のサイトで手続き</h3>  
    <p>【金融機関へ】をクリックすると、各金融機関のページに進みますので、手続きをお願いします。<br>
    <p align="center"><?= Html::img('@web/img/how_to_debit_09.jpg') ?></p>
    <p><br></p>   
    
    <h3 id="finish">４.登録完了</h3>  
    <p>各金融機関で、登録が完了しますと、以下のようなメールが送信されます。これで、お客様の作業は完了となります。<br>
    <p align="center"><?= Html::img('@web/img/how_to_debit_095.jpg') ?></p>
    <p><br></p>   

    <h3 id="response"> ５.ご登録の確認</h3>      
    <p><font color="#990066">ご登録の確認ができるのは、<strong>２～５営業日後</strong>となっております。</font>それまでは、代引でのご購入となります。<br>
       ご登録の確認が完了しますと、以下のような画面となりますので、それまでお待ちください。<font color="#990066">再度、ご登録の必要はございません。</font><br>
       この状態で、口座振替でお買い物をして頂けるようになります。<br>
       <font color="#990066">※各金融機関のご登録は、１日１回までとなっております。口座を変更されたい場合は、翌日以降改めて行ってください。</font></p>
    <p align="center"><?= Html::img('@web/img/how_to_debit_10.jpg') ?></p>
    <p><br></p>   
    <p>２～５営業日以上たっても、画面が上のようにならない場合は、<?= Html::a('お問い合わせ',['contact'], ['target'=>'_blank']) ?>よりご連絡をお願いします。<br>
    <p><br></p>   
    <h3 id="start"> ６.口座振替開始</h3>      
    <p>ご登録の確認ができますと、カートのお支払い方法に口座振替が設定されます。代引に変更することも可能です。<br>
    <p align="center"><?= Html::img('@web/img/how_to_debit_11.jpg') ?></p>
    <p><br></p>   
    <p>口座振替でご購入された金額は、月末締めで合算し、翌月26日付で自動引落しとなります。<br>
       自動引落しができなかった場合、豊受モールのご利用を制限させていただくことがあります。</p>

    <p><br></p>   
</div>