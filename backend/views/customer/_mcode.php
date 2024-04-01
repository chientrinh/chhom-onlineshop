<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/customer/_mcode.php $
 * $Id: _mcode.php 2806 2016-07-31 08:26:01Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Customer
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => \common\models\Membercode::find()->andWhere([
            'customer_id' => $model->customer_id,
        ])->andWhere(['not',['directive'=>null]])
          ->orderBy('status DESC'),

        'pagination' => false,
        'sort' => false,
    ]),
    'layout'  => '{items}',
    'tableOptions' => ['class'=>'table-condensed'],
    'showHeader' => false,
    'columns' => [
        'directive',
        [
            'attribute' => 'migrate_id',
            'contentOptions' => ['class'=>'text-right'],
        ],
        [
            'attribute' => 'code',
            'format'    => 'html',
            'value'     => function($data){ return Html::a('<i class="glyphicon glyphicon-log-out"></i>',['/membercode/view','id'=>$data->code]); },
        ],
    ],
]); ?>

</div>
