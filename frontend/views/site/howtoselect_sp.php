<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/howtoselect_sp.php $
 * $Id: howtoselect_sp.php 4067 2018-11-28 08:10:14Z kawai $
 *
 * @var $this \yii\web\View
 */

$title = "商品の選び方（一般商品）スマートフォンの場合";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Howtoselect';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>
<div class="site-guide">

    <h3>商品の選び方（一般商品）スマートフォンの場合</h3>
    <table>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_sp_00.jpg',['width'=>'200','height'=>'280','style'=>'margin:20px','alt'=>'お買い物方法0']) ?>
            </td>
            <td>
                 「商品名またはキーワード」の欄に、<br>
                 購入したい商品名を入力して、検索することができます。<br>
                 下にスクロールすると、カテゴリ一一覧がありますので、<br>
                 こちらから選択することも可能です。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_sp_01.jpg',['width'=>'200','height'=>'300','style'=>'margin:20px','alt'=>'お買い物方法1']) ?>
            </td>
            <td>
                 <strong>カテゴリー</strong><br>
            <ul>
                <li>自然食品・・・・・・・・・・野菜、加工食品など</li>
                <li>自然化粧品・・・・・・・・・スキンケア、ヘアケア用品など</li>
                <li>レメディー・ハーブ酒・・・・レメディー、MT、FE </li>
                <li>雑貨・・・・・・・・・・・・衣類（トミーローズ商品）、その他商品 </li>
            </ul>
                <br>
                <br>
                 歯磨き粉を購入する場合を例にしてみましょう。<br>
                 自然化粧品をタップします。<br>
                 <br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_sp_02.jpg',['width'=>'200','height'=>'350','style'=>'margin:20px','alt'=>'お買い物方法2']) ?>
            </td>
            <td>
                 <br>
                 サブカテゴリーをタップします。<br>
                 <br>
                 <br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_sp_03.jpg',['width'=>'200','height'=>'300','style'=>'margin:20px','alt'=>'お買い物方法3']) ?>
            </td>
            <td>
                 <br>
                 トゥースペーストをタップします。<br>
                 <br>
                 <br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_sp_04.jpg',['width'=>'200','height'=>'300','style'=>'margin:20px','alt'=>'お買い物方法4']) ?>
            </td>
            <td>
            <ul>
                <li>【カートに入れる】ボタン・・・お買い物かご（カート）に入れます</li>
                <li>【もっと見る】ボタン・・・・・商品の詳細が確認できます</li>
            </ul>

            </td>
        </tr>
    </table>  
    <h3>商品の詳細画面</h3>
    <table>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_sp_05.jpg',['width'=>'200','height'=>'300','style'=>'margin:20px','alt'=>'お買い物方法5']) ?>
            </td>
            <td>
                【カートに入れる】ボタンをタップすると、<br>
                　お買い物かご（カート）に入ります。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_sp_06.jpg',['width'=>'200','height'=>'300','style'=>'margin:20px','alt'=>'お買い物方法6']) ?>
            </td>
            <td>
                上のカートマーク、または、<br>
                左の【カートを見る】をタップすると、<br>
                ご注文の確認の画面に進みます。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_sp_07.jpg',['width'=>'200','height'=>'350','style'=>'margin:20px','alt'=>'お買い物方法7']) ?>
            </td>
            <td>
                お買い物を続けたい場合は、<br>
                【お買い物を続ける】ボタンをタップして下さい。<br>
                <br>
                ご注文の確定は、<br>
                <?= Html::a('お買い物の確定方法',['howtobuy'], ['target'=>'_blank']) ?>をご覧下さい。<br>
            </td>
        </tr>
    </table>

</div>