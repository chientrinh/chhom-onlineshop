<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/howtologin_sp.php $
 * $Id: howtologin_sp.php 4018 2018-09-07 11:19:45Z naito $
 *
 * @var $this \yii\web\View
 */

$title = "ログイン方法（スマートフォンの場合）";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Howtologin_ph';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>
<div class="site-guide">

    <h3>ログイン方法（スマートフォンの場合）</h3>
    <table>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_login_sp_01.jpg',['width'=>'200','height'=>'250','style'=>'margin:20px','alt'=>'ログイン方法1']) ?>
            </td>
            <td>
                 【ログイン】をタップすると、ログイン画面に進みます。
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_login_sp_02.jpg',['width'=>'200','height'=>'300','style'=>'margin:20px','alt'=>'ログイン方法2']) ?>
            </td>
            <td>
                ご登録されている、メールアドレスとパスワードを入力し、<br>
                【ログイン】ボタンをタップしてください。<br>
            </td>
        </tr>

    </table>
    <h3 id="passwd">パスワード設定</h3>
    <table>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_login_sp_03.jpg',['width'=>'200','height'=>'300','style'=>'margin:20px','alt'=>'ログイン方法3']) ?>
            </td>
            <td>
                パスワードを忘れた方、未設定の方は、<br>
                【パスワードを初期化】ボタンをタップしてください。<br>
                 パスワード設定画面に進みます。<br>
            </td>
         </tr>
         <tr>
            <td>
                 <?= Html::img('@web/img/how_to_login_sp_04.jpg',['width'=>'200','height'=>'200','style'=>'margin:20px','alt'=>'ログイン方法4']) ?>
            </td>
            <td>
                ご登録されている、メールアドレスを入力し、<br>
                【送信】ボタンをタップしてください。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_login_sp_05.jpg',['width'=>'200','height'=>'200','style'=>'margin:20px','alt'=>'ログイン方法5']) ?>
            </td>
            <td>
                入力されたメールアドレスに、メールが送信されます。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_login_06.jpg',['width'=>'200','height'=>'150','style'=>'margin:20px','alt'=>'ログイン方法6']) ?>
            </td>
            <td>
                送信されたメールの、本文中のurlをタップして下さい。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_login_sp_07.jpg',['width'=>'200','height'=>'250','style'=>'margin:20px','alt'=>'ログイン方法7']) ?>
            </td>
            <td>
                ご登録されているメールアドレスとパスワードを入力し、<br>
                【保存】ボタンをタップして下さい。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_login_sp_08.jpg',['width'=>'200','height'=>'300','style'=>'margin:20px','alt'=>'ログイン方法8']) ?>
            </td>
            <td>
                パスワードが設定されました。<br>
                【ログイン】ボタンをタップして、ログインして下さい。<br>
            </td>
        </tr>
   </table>  
</div>