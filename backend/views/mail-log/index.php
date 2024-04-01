<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/mail-log/index.php $
 * $Id: index.php 1157 2015-07-15 13:01:02Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

?>
<div class="mail-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'mailer_id',
            [
                'attribute' => 'date',
                'format'    => 'html',
                'value'     => function($model){ return Html::a($model->date, ['view','id'=>$model->mailer_id]); },
            ],
            'subject',
            'tbl',
            'pkey',
        ],
    ]); ?>

</div>
