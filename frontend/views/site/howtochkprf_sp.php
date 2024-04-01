<?php
use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/site/howtochkprf_sp.php $
 * $Id: howtochkprf_sp.php 4001 2018-08-24 06:09:36Z naito $
 *
 * @var $this \yii\web\View
 */

$title = "会員番号及び会員情報の確認方法（スマートフォンの場合）";
$this->params['breadcrumbs'][] = $title;
$this->params['body_id']       = 'Howtochkprf_sp';
$this->title = implode(' | ',array_merge(array_reverse($this->params['breadcrumbs']),[Yii::$app->name]));

?>
<div class="site-guide">

    <h3>会員番号及び会員情報の確認方法（スマートフォンの場合）</h3>
    <table>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_chk_prf_sp_01.jpg',['width'=>'200','height'=>'350','style'=>'margin:20px','alt'=>'会員情報の確認方法1']) ?>
            </td>
            <td>
                メニューをタップします。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_chk_prf_sp_02.jpg',['width'=>'400','height'=>'350','style'=>'margin:20px','alt'=>'会員情報の確認方法2']) ?>
            </td>
            <td>
                【モバイル会員証】をタップすると、<br>モバイル会員証が表示されますので、<br>会員番号を確認できます。<br><br>
                【マイページ】をタップすると、<br>マイページ画面に進みます。<br><br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_chk_prf_sp_035.jpg',['width'=>'200','height'=>'350','style'=>'margin:20px','alt'=>'会員情報の確認方法3.5']) ?>
            </td>
            <td>
                会員番号はこちらでも確認できます。<br>
            </td>
        </tr>        
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_chk_prf_sp_03.jpg',['width'=>'200','height'=>'350','style'=>'margin:20px','alt'=>'会員情報の確認方法3']) ?>
            </td>
            <td>
                会員情報を確認、変更したい場合は<br>
                【会員情報の確認・変更】をタップし、登録の確認画面に進みます。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_chk_prf_sp_04.jpg',['width'=>'200','height'=>'350','style'=>'margin:20px','alt'=>'会員情報の確認方法4']) ?>
            </td>
            <td>
                会員情報の確認をしてください。<br>
                <br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_chk_prf_sp_05.jpg',['width'=>'200','height'=>'150','style'=>'margin:20px','alt'=>'会員情報の確認方法5']) ?>
            </td>
            <td>
                変更する場合は、【編集する】ボタンをタップして下さい。<br>
            </td>
        </tr>
        <tr>
            <td>
                 <?= Html::img('@web/img/how_to_chk_prf_sp_06.jpg',['width'=>'200','height'=>'350','style'=>'margin:20px','alt'=>'会員情報の確認方法6']) ?>
            </td>
            <td>
                変更したい項目を修正して、【更新する】ボタンをタップすると、情報が更新されます。<br>
            </td>
        </tr>
    </table>  

</div>