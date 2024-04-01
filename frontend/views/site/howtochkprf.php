<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/howtochkprf.php $
 * $Id: howtochkprf.php 3605 2017-09-24 05:27:09Z naito $
 *
 * @var $this \yii\web\View
 */

$title = "会員情報の確認方法";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Howtochkprf';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>
<div class="site-guide">

    <h3>会員情報の確認方法</h3>
    <table>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_chk_prf_01.jpg',['width'=>'400','height'=>'300','style'=>'margin:20px','alt'=>'会員情報の確認方法1']) ?>
            </td>
            <td>
                【マイページ】をクリックし、マイページ画面に進みます。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_chk_prf_02.jpg',['width'=>'400','height'=>'200','style'=>'margin:20px','alt'=>'会員情報の確認方法2']) ?>
            </td>
            <td>
                【会員情報の確認・変更】をクリックし、登録の確認画面に進みます。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_chk_prf_03.jpg',['width'=>'400','height'=>'300','style'=>'margin:20px','alt'=>'会員情報の確認方法3']) ?>
            </td>
            <td>
                メールアドレスの確認をしてください。<br>
                変更する場合は、【編集する】ボタンをクリックして下さい。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_chk_prf_04.jpg',['width'=>'400','height'=>'500','style'=>'margin:20px','alt'=>'会員情報の確認方法4']) ?>
            </td>
            <td>
                変更したい項目を修正して、【更新する】ボタンをクリックすると、情報が更新されます。<br>
            </td>
        </tr>
    </table>  

</div>