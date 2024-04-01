<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/mail-log/view.php $
 * $Id: view.php 2827 2016-08-10 02:56:44Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\MailLog
 */

$this->registerCss('
th {width:5%;}
td {font-family:monospace;}
');
?>
<div class="mail-log-view">

    <p class="pull-right">
        <?php if($model->prev): ?>
        <?= Html::a("", ['view', 'id' => $model->prev->mailer_id], [
            'title' => "前のメール",
            'class' => 'btn btn-xs btn-default glyphicon glyphicon-chevron-left'
        ]) ?>
        <?php endif ?>

        <?php if($model->next): ?>
        <?= Html::a("", ['view', 'id' => $model->next->mailer_id], [
            'title' => "次のメール"
           ,'class' => 'btn btn-xs btn-default glyphicon glyphicon-chevron-right'
        ]) ?>
        <?php endif ?>
    </p>

    <h1>メール送信履歴：<?= $model->mailer_id ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'date',
            'to',
            'sender',
            'subject',
            [
                'attribute'=> 'body',
                'format'   => 'ntext',
            ],
            [
                'label' => 'tbl',
                'format'=> 'html',
                'value' => 
                ($model->tbl == \common\models\Purchase::tableName())
                ?  Html::a(sprintf("注文 %d", $model->pkey), ['/purchase/view','id'=>$model->pkey])
                : sprintf('%s:%s', $model->tbl, $model->pkey)
                ,
            ],
        ],
    ]) ?>

</div>
