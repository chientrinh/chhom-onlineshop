<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/profile/views/invoice/index.php $
 * $Id: index.php 3790 2017-12-22 10:49:08Z naito $
 *
 * @var $this  \yii\web\View
 */

use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;
use \common\models\InvoiceStatus;

$labels = ArrayHelper::map(InvoiceStatus::find()->all(),'istatus_id','name');
?>

<div id="profile-invoice-index">

    <h1 class="mainTitle">マイページ</h1>
    <p class="mainLead">このページでは出店企業・団体からのご優待や特典リンクをご案内します。</p>

    <div class="col-md-3">
        <div class="Mypage-Nav">
            <div class="inner">
                <h3>Menu</h3>
                <?= Yii::$app->controller->nav->run() ?>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <h2><span>請求書の履歴</span></h2>  
        <p><font color=#990066">※状態につきましては、こちらで入金を確認した月の、翌月の月初に反映されますのでご了承下さい。</font></p>

        <?= \yii\grid\GridView::widget([
            'dataProvider' => $provider,
            'layout' => '{items}{pager}',
            'columns' => [
                [
                    'attribute' => 'target_date',
                    'format'    => ['date', 'php:Y-m'],
                ],
                'due_total:currency',
                [
                    'attribute' => 'payment_id',
                    'value'     => function($data){ return ArrayHelper::getValue($data, 'payment.name'); },
                ],
                [
                    'attribute' => 'status',
                    'value'     => function($data)use($labels){ return ArrayHelper::getValue($labels, $data->status); },
                ],
                [
                    'label'  => null,
                    'format' => 'html',
                    'value'  => function($data){
                        $img = Html::img('/img/application-pdf.png',['alt'=>'pdf']);
                        return Html::a($img, ['view','id'=>$data->invoice_id]);
                    },
                ],
            ],
        ]) ?>
    </div>

</div>
