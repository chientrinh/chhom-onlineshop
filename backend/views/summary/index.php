<?php
/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/backend/views/summary/index.php $
 * @version $Id: index.php 3103 2016-11-24 05:38:13Z mori $
 *
 * @var $this yii\web\View
 */
$this->params['breadcrumbs'][] = ['label' => '販売管理', 'url' => ['index']];
?>

<div class="summary-default-index">
    <div class="col-md-2 col-xs-1">
    <?= \yii\bootstrap\Nav::widget([
        'options' => ['class' => 'nav nav-pills nav-stacked'],
        'items' => [
            ['url' => ['daily'], 'label' => '日次'],
            ['url' => ['monthly'], 'label' => '月次'],
            ['url' => ['purchase-item-csv'], 'label' => '売上明細CSV'],
            ['url' => ['agency-summary-csv'], 'label' => '代理店売上集計CSV'],
        ],
    ]) ?>
    </div>
</div>
