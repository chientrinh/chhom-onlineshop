<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/howtokit.php $
 * $Id: howtokit.php 3303 2017-05-20 06:17:55Z naito $
 *
 * @var $this \yii\web\View
 */

$title = "商品の選び方（キット補充）";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Howtokit';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>
<div class="site-guide">

    <h3>商品の選び方（キット補充）</h3>
    <table>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_kit_01.jpg',['width'=>'400','height'=>'300','style'=>'margin:20px','alt'=>'キット補充1']) ?>
            </td>
            <td>
                 【レメディー・ハーブ酒】をクリックします。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_kit_02.jpg',['width'=>'400','height'=>'400','style'=>'margin:20px','alt'=>'キット補充2']) ?>
            </td>
            <td>
                 左側のカテゴリー一覧から選びます。<br>
                 選んだカテゴリーの商品が一覧で表示されます。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_kit_03.jpg',['width'=>'400','height'=>'400','style'=>'margin:20px','alt'=>'キット補充3']) ?>
            </td>
            <td>
            <ul>
                <li>【カートに入れる】ボタン・・・お買い物かご（カート）に入れます
                <li>【もっと見る】ボタン・・・・・レメディーの詳細が確認できます</li>
            </ul>
            </td>
        </tr>
    </table>  
    <h3>レメディーの詳細画面</h3>
    <table>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_kit_06.jpg',['width'=>'400','height'=>'250','style'=>'margin:20px','alt'=>'キット補充6']) ?>
            </td>
            <td>
                【カートに入れる】ボタンをクリックすると、<br>
                カートに入ります。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_kit_07.jpg',['width'=>'400','height'=>'300','style'=>'margin:20px','alt'=>'キット補充7']) ?>
            </td>
            <td>
                左の【カートを見る】をクリックすると、<br>
                ご注文の確認の画面に進みます。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_kit_08.jpg',['width'=>'400','height'=>'280','style'=>'margin:20px','alt'=>'キット補充8']) ?>
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