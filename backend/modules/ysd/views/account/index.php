<?php
/**
 * $URL$
 * $Id$
 *
 * $provider ActiveDataProvider
 *
 */
use \yii\helpers\Html;
?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $provider,
    'filterModel'  => $searchModel,    
    'columns' => [
        [
            'attribute' => 'customer_id',
            'format'    => 'html',
            'value'     => function($data){ return Html::a($data->customer_id, ['view','id'=>$data->customer_id]); },
        ],
        [
            'attribute' => 'customer.name',
        ],
        [
            'label'     => '状態',
            'attribute' => 'expire_id',
            'format'    => 'text',
            'value'     => function($data){  return $data->expire->name; },
            'filter'    => \yii\helpers\ArrayHelper::map(\common\models\ysd\AccountStatus::find()->all(), 'expire_id','name'),
        ],
        'credit_limit:currency',
    ],
]) ?>
