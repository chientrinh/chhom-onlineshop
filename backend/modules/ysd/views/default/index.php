<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/ysd/views/default/index.php $
 * @version $Id: index.php 3103 2016-11-24 05:38:13Z mori $
 *
 * @var $this yii\web\View
 */

?>

<div class="ysd-default-index">

    <div class="col-md-2 col-xs-1">
    <?= \yii\bootstrap\Nav::widget([
        'options' => ['class' =>'nav nav-pills nav-stacked'],
        'items' => [
            ['url'=>['account/index'],'label'=>'口座'],
            ['url'=>['rrq/index'],'label'=>'登録依頼'],
            ['url'=>['rrs/index'],'label'=>'登録結果'],
            ['url'=>['trq/index'],'label'=>'振替依頼'],
            ['url'=>['trs/index'],'label'=>'振替結果'],
        ],
    ]) ?>
    </div>

</div>
