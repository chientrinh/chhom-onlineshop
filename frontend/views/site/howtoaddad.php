<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/howtoaddad.php $
 * $Id: howtoaddad.php 3303 2017-05-20 06:17:55Z naito $
 *
 * @var $this \yii\web\View
 */

$title = "お届け先の登録方法";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Howtoaddad';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>
<div class="site-guide">

    <h3>お届け先の登録方法</h3>
    <table>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_add_ad_01.jpg',['width'=>'400','height'=>'300','style'=>'margin:20px','alt'=>'お届け先の登録方法1']) ?>
            </td>
            <td>
                【マイページ】をクリックし、マイページ画面に進みます。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_add_ad_02.jpg',['width'=>'400','height'=>'200','style'=>'margin:20px','alt'=>'お届け先の登録方法2']) ?>
            </td>
            <td>
                【住所録】をクリックし、お届け先の追加・変更画面に進みます。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_add_ad_03.jpg',['width'=>'400','height'=>'200','style'=>'margin:20px','alt'=>'お届け先の登録方法3']) ?>
            </td>
            <td>
                マイページ/住所録/新しいお届け先の追加　画面に進みます。<br>
                必要事項を入力し、【追加】ボタンをクリックして下さい。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_add_ad_04.jpg',['width'=>'400','height'=>'300','style'=>'margin:20px','alt'=>'お届け先の登録方法4']) ?>
            </td>
            <td>
                マイページ/住所録/新しいお届け先の追加　画面に進みます。<br>
                必要事項を入力し、【追加】ボタンをクリックして下さい。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_add_ad_05.jpg',['width'=>'400','height'=>'250','style'=>'margin:20px','alt'=>'お届け先の登録方法5']) ?>
            </td>
            <td>
                新しいお届け先が登録されました。<br>
            </td>
        </tr>
    </table>  

</div>