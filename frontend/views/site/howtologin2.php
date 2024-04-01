<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/howtologin.php $
 * $Id: howtologin.php 3464 2017-06-30 09:55:04Z naito $
 *
 * @var $this \yii\web\View
 */

$title = "ログイン方法";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Howtologin';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>
<div class="site-guide">

    <h3>ログイン方法</h3>
    <table>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_login_01.jpg',['width'=>'400','height'=>'300','style'=>'margin:20px border:solid 5px #000000','alt'=>'ログイン方法1']) ?>
            </td>
            <td>
                 【ログイン】をクリックすると、ログイン画面に進みます。
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_login_02.jpg',['width'=>'400','height'=>'250','style'=>'margin:20px','alt'=>'ログイン方法2']) ?>
            </td>
            <td>
                ご登録されている、メールアドレスとパスワードを入力し、<br>
                【ログイン】ボタンをクリックしてください。<br>
                <br>
                ご登録されたメールアドレスを忘れた方は、
                豊受オーガニクスモール会員証の会員番号と仮パスワードでもログインができます。<br>
                
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_login_10.jpg',['width'=>'300','height'=>'100','style'=>'margin:20px','alt'=>'会員証']) ?>
            </td>
            <td>
                会員番号と仮パスワードでログインされた場合は、<br>
                マイページからメールアドレスを確認してください。<br>
                <?= Html::a('マイページの確認方法はこちら',['howtochkprf'], ['target'=>'_blank']) ?><br>
                <br>
            </td>
        </tr>
    </table>
    <h3 id="passwd">パスワード設定</h3>
    <table>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_login_03.jpg',['width'=>'400','height'=>'250','style'=>'margin:20px','alt'=>'ログイン方法3']) ?>
            </td>
            <td>
                パスワードを忘れた方、未設定の方は、<br>
                【パスワードを初期化】ボタンをクリックしてください。<br>
                 パスワード設定画面に進みます。<br>
            </td>
         </tr>
         <tr>
            <td>
                 <?= Html::img('@web/img/how_to_login_04.jpg',['width'=>'400','height'=>'175','style'=>'margin:20px','alt'=>'ログイン方法4']) ?>
            </td>
            <td>
                ご登録されている、メールアドレスを入力し、<br>
                【送信】ボタンをクリックしてください。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_login_05.jpg',['width'=>'400','height'=>'175','style'=>'margin:20px','alt'=>'ログイン方法5']) ?>
            </td>
            <td>
                入力されたメールアドレスに、メールが送信されます。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_login_06.jpg',['width'=>'400','height'=>'170','style'=>'margin:20px','alt'=>'ログイン方法6']) ?>
            </td>
            <td>
                送信されたメールの、本文中のurlをクリックして下さい。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_login_07.jpg',['width'=>'400','height'=>'200','style'=>'margin:20px','alt'=>'ログイン方法7']) ?>
            </td>
            <td>
                ご登録されているメールアドレスとパスワードを入力し、<br>
                【保存】ボタンをクリックして下さい。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_login_08.jpg',['width'=>'400','height'=>'250','style'=>'margin:20px','alt'=>'ログイン方法8']) ?>
            </td>
            <td>
                パスワードが設定されました。<br>
                【ログイン】ボタンをクリックして、ログインして下さい。<br>
            </td>
        </tr>
   </table>  
</div>