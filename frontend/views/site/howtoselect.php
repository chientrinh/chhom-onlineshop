<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/howtoselect.php $
 * $Id: howtoselect.php 4067 2018-11-28 08:10:14Z kawai $
 *
 * @var $this \yii\web\View
 */

$title = "商品の選び方（一般商品）";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Howtoselect';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>
<div class="site-guide">

    <h3>商品の選び方（一般商品）</h3>
    <table>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_01.jpg',['width'=>'400','height'=>'300','style'=>'margin:20px','alt'=>'お買い物方法1']) ?>
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
                 歯磨き粉を購入する場合を例にしてみましょう<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_02.jpg',['width'=>'400','height'=>'300','style'=>'margin:20px','alt'=>'お買い物方法2']) ?>
            </td>
            <td>
                 <br>
                 左側のカテゴリー一覧から選びます。<br>
                 選んだカテゴリーの商品が一覧で表示されます。<br>
                 <br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_03.jpg',['width'=>'400','height'=>'300','style'=>'margin:20px','alt'=>'お買い物方法3']) ?>
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
                 <?= Html::img('@web/img/how_to_buy_04.jpg',['width'=>'400','height'=>'300','style'=>'margin:20px','alt'=>'お買い物方法4']) ?>
            </td>
            <td>
                【カートに入れる】ボタンをクリックすると、<br>
                　お買い物かご（カート）に入ります。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_05.jpg',['width'=>'400','height'=>'300','style'=>'margin:20px','alt'=>'お買い物方法5']) ?>
            </td>
            <td>
                右上の【カート】　または、<br>
                左の【カートを見る】をクリックすると、<br>
                ご注文の確認の画面に進みます。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_06.jpg',['width'=>'400','height'=>'300','style'=>'margin:20px','alt'=>'お買い物方法6']) ?>
            </td>
            <td>
                お買い物を続けたい場合は、<br>
                【お買い物を続ける】ボタンをクリックして下さい。<br>
                <br>
                ご注文の確定は、<br>
                <?= Html::a('お買い物の確定方法',['howtobuy'], ['target'=>'_blank']) ?>をご覧下さい。<br>
            </td>
        </tr>
    </table>

</div>