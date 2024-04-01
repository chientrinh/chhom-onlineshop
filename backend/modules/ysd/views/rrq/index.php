<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/ysd/views/rrs/view.php $
 * @version $Id: view.php 1961 2016-01-11 01:39:26Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

?>
<div class="register-request-index">

    <h1>登録依頼</h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'created_at',
                'format'    => 'html',
                'value'     => function($data)
                {
                    $label = Yii::$app->formatter->asDate($data->created_at,'php:Y-m-d H:i');
                    return Html::a($label,['view','id'=>$data->rrq_id]);
                },
            'filterInputOptions' => ['placeholder'=>'yyyy-mm-dd','class'=>'form-control'],
            ],
            [
                'attribute' => 'userno',
                'value'     => function($data){ return ($c = $data->customer) ? $c->name : $data->userno; },
                'filterInputOptions' => ['placeholder'=>'数字のみ入力可','class'=>'form-control'],
            ],
            'ip',
            'feedback',
            'emsg:ntext',
        ],
    ]); ?>

</div>
