<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/howtomk_tekiyosyo.php $
 * $Id: howtomk_tekiyosyo.php 3687 2017-10-21 04:19:24Z naito $
 *
 * @var $this \yii\web\View
 */

$title = "適用書の作成方法";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Howtomk_tekiyosyo';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>
<div class="site-guide">

    <p><font color=#990066"> ※「豊受モール」で作成した適用書は、「豊受モール」ならびに「ホメオパシージャパン正規販売店」でご購入が可能です。<br>
        「ホメオパシージャパンオンラインショップ」ではご購入できませんので、
        適用書発行の際には、クライアント様にその旨をお知らせくださいますようお願い致します。</font><br><br> </p>
    <p><font color=#990066"> ※ 当サイトをご利用いただくためには、Chrome　または　Firefox　を推奨いたします。
    IE（インターネットエクスプローラー）には対応しておりませんので、ご注意下さい。</font><br><br> </p>
        
    <h2>適用書の作成方法</h2>
    <p><br></p>
    <p>◆　適用書画面で以下のことができるようになりました　◆</p>
    <p>・適用書を保存して、履歴を見ることができます。</p>
    <p>・過去に作成した適用書の履歴をコピーして作成することができます。</p>
    <p><br></p>
    <p><br></p>
    <p>ログイン後、【マイページ】をクリックし、マイページ画面に進みます。</p>
     <p align="center"><?= Html::img('@web/img/how_to_tekiyousyo_01.jpg') ?></p>
    <p><br></p>
    <p>【適用書】をクリックすると、適用書の一覧画面に進みます。</p>
    <p align="center"><?= Html::img('@web/img/how_to_tekiyousyo_02.jpg') ?></p>
    <p><br></p>
    <h3>新規作成</h3>
    <p>【新規作成】ボタンをクリックすると、適用書作成画面に進みます。</p>
    <p align="center"><?= Html::img('@web/img/how_to_tekiyousyo_03.jpg') ?></p>
    <p><br></p>
    <p>明細を追加していきます。<br>
    <p>メニュー概要</p>
    <ul>
    <li>オリジナル作成・・・オリジナル小、オリジナル大、オリジナルアルポ、オリジナルマザーチンクチャーの作成</li>
    <li>単品レメディー・・・レメディー単品での選択</li>
    <li>単品MT ・・・・・・マザーチンクチャーの選択</li>
    <li>FE2、FE  ・・・・・フラワーエッセンスの選択</li>
    <li>特別レメディー ・・レメディーマシンによるレメディーの製造</li> 
    </ul>

    <p align="center"><?= Html::img('@web/img/how_to_tekiyousyo_04.jpg') ?></p>
    <p>オリジナル作成<br></p>
    <p align="center"> <?= Html::a('◆◆◆オリジナルレメディーの作成方法はこちら◆◆◆',['howtomk_original'], ['target'=>'_blank','style'=>'color:#990066']) ?></p>
    <p><br></p>
    <p align="center"><?= Html::img('@web/img/how_to_tekiyousyo_05.jpg') ?></p>
    <p><br></p>
    <p>単品レメディー、単品MT、FE2、FE</p>
    <p align="center"><?= Html::img('@web/img/how_to_tekiyousyo_06.jpg') ?></p>
    <p><br></p>
    <p align="center">短縮形、名前、ポーテンシー、容器での検索、絞り込みができます。</p>
    <p align="center"><?= Html::img('@web/img/how_to_tekiyousyo_07.jpg') ?></p>
    <p><br></p>
    <p>明細の追加が終了したら、【適用書を表示】ボタンをクリックし、適用書の画面に進みます。</p>
    <p align="center"><?= Html::img('@web/img/how_to_tekiyousyo_08.jpg') ?></p>
    <p><br></p>
    <p>目安、メモ、備考などを入力します。</p>
    <p align="center"><?= Html::img('@web/img/how_to_tekiyousyo_09.jpg') ?></p>
    
    <p><br></p>
    <p>クライアントの入力をします。豊受モールに登録されているクライアントの場合は、検索して設定できます。【＋】をクリックします。</p>
    <p align="center"><?= Html::img('@web/img/how_to_tekiyousyo_10.jpg') ?></p>
    <p>クライアントを電話番号（または会員証番号）で検索し、選択します。</p>
    <p><br></p>
    <p align="center"><?= Html::img('@web/img/how_to_tekiyousyo_11.jpg') ?></p>
    <p>豊受モールに登録がないクライアントの場合は、手入力で設定できます。<br>
    <p><br></p>
    <p align="center"><?= Html::img('@web/img/how_to_tekiyousyo_12.jpg') ?></p>
    <p><br></p>
    <p>センター名と電話番号を入力します。省略もできます。</p>
    <p align="center"><?= Html::img('@web/img/how_to_tekiyousyo_13.jpg') ?></p>
    <p><br></p>
    <p>適用書を印刷する前に、プレビューで確認しましょう。</p>
    <p align="center"><?= Html::img('@web/img/how_to_tekiyousyo_14.jpg') ?></p>
    <p><br></p>
    <p>【戻る】をクリックして、適用書画面に戻ります。</p>
    <p align="center"><?= Html::img('@web/img/how_to_tekiyousyo_15.jpg') ?></p>
    <p><br></p>
    <p>問題がなければ、【完了】ボタンをクリックし、適用書を保存します。保存された適用書は、一覧から検索することができます。<br>
    【すべて削除】ボタンをクリックすると、明細が全て削除されます。</p>
    <p align="center"><?= Html::img('@web/img/how_to_tekiyousyo_16.jpg') ?></p>
    <p><br></p>
    <p>適用書を印刷するには、【印刷】ボタンをクリックします。<br>
       適用書を続けて作成したい場合や、今まで作成した適用書を確認したい場合は、【履歴（一覧）に戻る】ボタンをクリックします。<br>
       【無効にする】ボタンをクリックすると、この適用書は無効となります。<br>
       【再作成】ボタンをクリックすると、この適用書は無効となり、この適用書のコピーが新規作成されます。
    </p>
    <p align="center"><?= Html::img('@web/img/how_to_tekiyousyo_17.jpg') ?></p>
    <p><br></p>
    <p>【印刷】ボタンをクリックすると、下のような画面になります。<br>
        ブラウザの【印刷】をクリックして、印刷できます。（ブラウザによって、印刷ボタンが異なります。）</p>
    <p align="center"><?= Html::img('@web/img/how_to_tekiyousyo_18.jpg') ?></p>
    <p><br><br><br></p> 
    <h3>一覧から検索して再作成</h3>
    <p>一覧から【適用書NO】をクリックして、保存された適用書を開きます。</p>
    <p align="center"><?= Html::img('@web/img/how_to_tekiyousyo_19.jpg') ?></p>
    <p><br></p>
    <p>【再作成】ボタンをクリックすると、この適用書は無効となり、この適用書のコピーが新規作成されます。<br>
       　一覧に戻りたい場合は、【履歴（一覧）に戻る】ボタンをクリックします。<br>
       【無効にする】ボタンをクリックすると、この適用書は無効となります。<br>
    </p>
    <p align="center"><?= Html::img('@web/img/how_to_tekiyousyo_20.jpg') ?></p>
    <p><br></p>
    <p>この適用書のコピーをベースに修正、追加、削除などの編集をして、新規に作成することができます。</p>
    <p align="center"><?= Html::img('@web/img/how_to_tekiyousyo_21.jpg') ?></p>
    <p><br></p> 

</div>