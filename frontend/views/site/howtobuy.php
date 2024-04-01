<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/howtobuy.php $
 * $Id: howtobuy.php 4076 2018-11-30 05:17:11Z mori $
 *
 * @var $this \yii\web\View
 */

$title = "お買い物の確定方法";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Howtobuy';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>
<div class="site-guide">

    <h3>お買い物の確定方法</h3>
    <table>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_06.jpg',['width'=>'400','height'=>'300','style'=>'margin:20px','alt'=>'お買い物方法6']) ?>
            </td>
            <td>
                ご注文の商品、お支払い金額、お届け先、お届け日時等をよくご確認の上、<br>
                【注文を確定する】ボタンをクリックしてください。<br>
                <br>
                <br>
                数量の変更方法は、<?= Html::a('こちら',['howtochange_qty'], ['target'=>'_blank']) ?>をご覧ください。
                <br>
                <br>
                お買い物を続けたい場合は、<br>
                【お買い物を続ける】ボタンをクリックして下さい。
            </td>
        </tr>
    </table>
    
    <h3>ポイントの使用方法</h3>
    <table>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_point_01.jpg',['width'=>'400','height'=>'300','style'=>'margin:20px','alt'=>'ポイント使用方法1']) ?>
            </td>
            <td>
                ポイント値引きの【変更】ボタンをクリックします。<br>
                （現在１０３ポイントまで使用できます。）<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_point_02.jpg',['width'=>'200','height'=>'100','style'=>'margin:20px','alt'=>'ポイント使用方法2']) ?>
            </td>
            <td>
                ポイント値引きの入力画面が開きます。<br>
                現在１０３ポイントまで使用できます。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_point_03.jpg',['width'=>'200','height'=>'100','style'=>'margin:20px','alt'=>'ポイント使用方法3']) ?>
            </td>
            <td>
                100ポイント使用してみます。<br>
                100と入力して、【更新】ボタンをクリックします。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_point_04.jpg',['width'=>'400','height'=>'250','style'=>'margin:20px','alt'=>'ポイント使用方法4']) ?>
            </td>
            <td>
                ポイント値引きが設定されました。<br>
            </td>
        </tr>
    </table>
    
    <h3>お届け先の変更方法</h3>
    <table>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_07.jpg',['width'=>'400','height'=>'300','style'=>'margin:20px','alt'=>'お買い物方法7']) ?>
            </td>
            <td>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                お届け先を変更したい場合は、<br>
                お届け先の【変更】ボタンをクリックして下さい。。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_08.jpg',['width'=>'400','height'=>'210','style'=>'margin:20px','alt'=>'お買い物方法8']) ?>
            </td>
            <td>
                登録されたお届け先より、お届け先を選び、<br>
                【決定】ボタンをクリックして下さい。<br>
                <br>
                お届け先は、あらかじめ、マイページより登録をしておいて下さい。<br>
                <?= Html::a('マイページのお届け先登録方法はこちら',['howtoaddad'], ['target'=>'_blank']) ?><br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_09.jpg',['width'=>'400','height'=>'300','style'=>'margin:20px','alt'=>'お買い物方法9']) ?>
            </td>
            <td>
                お届け先が変更されました。<br>
            </td>
        </tr>
    </table>  
    
    <h3>お届け日時の指定方法</h3>
    <table>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_10.jpg',['width'=>'400','height'=>'300','style'=>'margin:20px','alt'=>'お買い物方法10']) ?>
            </td>
            <td>
                お届け日時を指定したい場合は、<br>
                お届け日時の【変更】ボタンをクリックして下さい。<br>
                <br>
                指定がない場合は、最短日時でお届けされます。
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_11.jpg',['width'=>'400','height'=>'200','style'=>'margin:20px','alt'=>'お買い物方法11']) ?>
            </td>
            <td>
                配達日時の指定画面に進みます。<br>
                <br>
                希望日と、時間帯の右端にある▼をクリックします。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_12.jpg',['width'=>'400','height'=>'200','style'=>'margin:20px','alt'=>'お買い物方法12']) ?>
            </td>
            <td>
                <br>
                希望日と時間帯を選択して下さい。<br>
                <br>
                希望日を【指定なし】、時間帯だけを指定すると、<br>
                最短日での時間帯指定となります。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_13.jpg',['width'=>'400','height'=>'200','style'=>'margin:20px','alt'=>'お買い物方法13']) ?>
            </td>
            <td>
                【更新】ボタンをクリックして下さい。<br>
            </td>
        </tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_14.jpg',['width'=>'400','height'=>'320','style'=>'margin:20px','alt'=>'お買い物方法14']) ?>
            </td>
            <td>
                配達日時が変更されました。<br>
            </td>
        </tr>
    </table>  
    
    <h3>注文の確定</h3>
    <table>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_15.jpg',['width'=>'400','height'=>'320','style'=>'margin:20px','alt'=>'お買い物方法15']) ?>
            </td>
            <td>
                【注文を確定する】ボタンをクリックすると、<br>
                お買い物が確定し、完了します。<br>
                ご注文の商品、お支払い金額、お届け先、お届け日時等をご確認の上、<br>
                【注文を確定する】ボタンをクリックしてください。<br>
                <br>                
                ご注文した商品の会社によって複数のカートに分かれる場合があります<br>
                【２店舗一括発送カート】 以下の会社の商品<br>
                ホメオパシージャパン株式会社<br>
                ホメオパシック・エデュケーション株式会社<br>
                【 豊受自然農カート】 豊受自然農のの商品<br>
                【トミーローズカート】 トミーローズの商品<br>
                <br>
                複数のカートに分かれた場合はそれぞれのカートで<br>
                【注文を確定する】ボタンをクリックしてください。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_16.jpg',['width'=>'400','height'=>'230','style'=>'margin:20px','alt'=>'お買い物方法16']) ?>
            </td>
            <td>
                ご注文を確定すると、注文確認メールが送信されますので、ご確認下さい。<br>
                <br>
                ご注文内容は、マイページでご確認いただけます。<br>
                <br>
                【マイページご購入の履歴　注文番号******】をクリックして下さい。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_buy_17.jpg',['width'=>'400','height'=>'350','style'=>'margin:20px','alt'=>'お買い物方法17']) ?>
            </td>
            <td>
                <br>
                マイページ　ご購入の履歴　が表示されます。<br>
                <br>
            </td>
        </tr>
    </table>  

</div>
