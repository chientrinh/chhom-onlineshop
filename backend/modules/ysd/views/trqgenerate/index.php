<?php

use yii\helpers\Html;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/ysd/views/rrs/view.php $
 * @version $Id: view.php 1961 2016-01-11 01:39:26Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel \common\models\TransferRequest
 * @var $year     integer
 * @var $month    integer
 * @var $invoices integer
 */

$prev = (object) [
    'year'  => (1 == $month) ? ($year -1) : $year,
    'month' => (1 == $month) ? 12         : $month - 1,
    ];
$next = (object) [
    'year'  => (12 == $month) ? ($year +1) : $year,
    'month' => (12 == $month) ? 1          : $month + 1,
];

$summary = sprintf('%04d 年 %02d 月 分を表示しています', $year, $month);
$jscode = "
    $(function() {
        $('#csvuploadform-file').on('change', function() {
            var file = this.files[0];
            if(file != null) {
                console.log(file.name); // ファイル名をログに出力する
                $('#upload').removeAttr('disabled');
            } else {
                $('#upload').attr('disabled', 'disabled');
            }
        });
    });
";

$this->registerJs($jscode);
?>

<div class="transfer-request-index">

    <div class="pull-right">
        <?= Html::a(Html::tag('span','',['class'=>'glyphicon glyphicon-chevron-left']),[
            'index',
            'year' => $prev->year,
            'month'=> $prev->month,
        ],['class'=>'btn btn-xs btn-default']) ?>

        <strong><?= sprintf('%04d-%02d', $year, $month) ?></strong>

        <?= Html::a(Html::tag('span','',['class'=>'glyphicon glyphicon-chevron-right']),[
            'index',
            'year' => $next->year,
            'month'=> $next->month,
        ],['class'=>'btn btn-xs btn-default']) ?>
    </div>

    <h1>振替依頼</h1>

    <p>
        <?= $summary ?>
    </p>



    <?php if($dataProvider->totalCount && $invoices && ($invoices != $dataProvider->totalCount)): ?>
    <?php endif ?>
    <p>
        発行ボタンをクリックすると、当月の請求書・振替依頼を一括で発行します。発行後、ヤマトシステムへの振替依頼データ（CSV）が出力されます。
　　</p>
        <?= Html::a('振替依頼発行',['create','year'=>$year,'month'=>$month, 'csv_file' => $csvModel->file ? $csvModel->file->name : null],['class'=>'btn btn-success','title'=>'当月の振替依頼を発行しCSV出力します']) ?>
    <br><br><br><br>
    <p>
        ※前月分の振替結果ファイル（CSV）を下のフォームから「送信」しセットしておくと、そのファイルから「未落ち分」を検出して出力します（予定）
    </p>
    <?php echo $this->render('upload', ['model'=>$csvModel]);?>

</div>
