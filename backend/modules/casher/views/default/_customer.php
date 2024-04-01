<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/views/default/_customer.php $
 * $Id: _customer.php 4221 2020-01-13 08:24:29Z mori $
 *
 * $searchModel  Model
 * $dataProvider ActiveDataProvider of \backend\models\Customer
 */
use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;

?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'columns'   => [
        [
            'attribute' => 'name',
            'format'    => 'html',
            'value'     => function($data) { return $data->parent ? $data->name."(".$data->parent->name."の家族会員)" : $data->name; },
        ],
        [
           'attribute' => 'kana',
           'format'    => 'html',
           'value'     => function($data){ return $data->parent ? Html::a($data->kana, ['apply', 'id'=>$data->parent->customer_id, 'target'=>'customer']) : Html::a($data->kana, ['apply', 'id'=>$data->customer_id, 'target'=>'customer']); },
           ],
        [
            'attribute' => 'point',
            'format'    => 'integer',
            'contentOptions' => ['class'=>'text-right'],
        ],
        [
            'attribute' => 'pref_id',
            'format'    => 'html',
            'value'     => function($data){ return $data->pref
                        ? Html::tag('span', $data->pref->name, ['title'=>$data->addr])
                        : ''; },
            'filter'    => ArrayHelper::map(\common\models\Pref::find()->all(), 'pref_id', 'name'),
        ],
        [
            'attribute' => 'tel',
            'format'    => 'html',
            'value'     => function($data){ return Html::tag('code', $data->tel); },
        ],
        [
            'attribute' => 'code',
            'format'    => 'html',
            'value'     => function($data)
            {
                return $data->membercode ? Html::tag('code',$data->membercode->code) : ''; 
            },
        ],
        [
            'label'     => '',
            'format'    => 'html',
            'value'     => function($data){ return Html::a('詳細', ['/customer/view','id'=>$data->customer_id],['class'=>'btn btn-xs btn-info']); },
            'contentOptions' => ['class'=>'text-center'],
        ],
    ],
]) ?>
