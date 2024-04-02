<?php

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/invoice/views/admin/index.php $
 * $Id: index.php 3848 2018-04-05 09:12:44Z mori $
 */

use \yii\helpers\Html;
use yii\helpers\Url;

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
    var lockId = 'lockId';

    // 画面操作を有効にする
    unlockScreen(lockId);

    $('#print-all').on('click', function() {
        if (! confirm('当月度の請求書をバッチ処理で一括PDF出力します。この間、しばらく操作出来なくなりますがよろしいですか。')){
            return false;
        }
        // 画面操作を無効する
        lockScreen(lockId);
    });

    /* 画面操作を無効にする　*/
    function lockScreen(id) {

        var divTag = $('<div />').attr('id', id);

        /* スタイル設定 */
        divTag.css('z-index', '999')
              .css('position', 'fixed')
              .css('top', '0px')
              .css('left', '0px')
              .css('right', '0px')
              .css('bottom', '0px')
              .css('background', 'rgba(0,0,0,0.75)')
              .css('opacity', '0.01');

        $('*').append(divTag);
    }

    /* 画面操作無効を解除する */
    function unlockScreen(id) {

        /* 画面を覆っているタグを削除する */
        $('#' + id).remove();
    }
";
$this->registerJs($jscode, \yii\web\View::POS_LOAD);
?>

<div class="invoice-default-index">

    <p class="pull-right">
        <?= Html::a(Html::tag('span','',['class'=>'glyphicon glyphicon-chevron-left']),[
            'index',
            'year' => $prev->year,
            'month'=> $prev->month,
        ],['class'=>'btn btn-xs btn-default']) ?>

        <strong><?= sprintf('%04d-%02d', $year, $month) ?></strong>

        <?= html::a(Html::tag('span','',['class'=>'glyphicon glyphicon-chevron-right']),[
            'index',
            'year' => $next->year,
            'month'=> $next->month,
        ],['class'=>'btn btn-xs btn-default']) ?>
    </p>

    <h1>請求書 <small>発行</small> </h1>

    <p>
        <?= $summary ?>
    </p>

    <?php if ($this->context->id === 'admin'): ?>
        <?= Html::a('CSV表示', Url::current(['format'=>'csv']), ['class'=>'btn btn-default']) ?>
    <?php endif; ?>

    <?= $this->render('_grid', [
        'dataProvider'=> $dataProvider,
        'searchModel' => $searchModel,
    ]) ?>

    <?php if(0 == $dataProvider->totalCount): ?>

        <?php if($customers = count(\common\modules\invoice\components\InvoiceMaker::getCustomers($year, $month))): ?>
            <p>発行待ちの請求書が <?= $customers ?> 通あります</p>
            <?php if(time() < strtotime(sprintf('%04d-%02d-t', $year, $month))): ?>
                <p class="alert alert-warning">当月は締め日を迎えていません。原則として発行ボタンのクリックは控えてください。</p>
            <?php endif ?>
        <?php else: ?>
            <p>発行待ちの請求書はありません</p>
        <?php endif ?>

    <?php endif ?>

    <?= Html::a(($dataProvider->count ? '再' : null).'発行する',['create','year'=>$year,'month'=>$month],['class'=>'btn btn-success']) ?>

    <?= Html::a('当月度のPDFを一括出力する',['print-all','year'=>$year,'month'=>$month],['class' => 'btn btn-danger', 'id'  => 'print-all']) ?>

</div>
