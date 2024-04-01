<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\ChangeLog;

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $model common\models\Commission
 * 
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/commission/view.php $
 * $Id: view.php 2797 2016-07-31 01:53:11Z mori $
 */

$title = sprintf('%06d',$model->commision_id);
$this->params['breadcrumbs'][] = ['label' => $title];

$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels);
$labels[] = Yii::$app->name;
$this->title = implode(' | ', $labels);

?>
<div class="commission-view">

    <p class="pull-right">
        <?= Html::a('修正', ['update', 'id' => $model->commision_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <h1><?= $title ?></h1>

    <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'company_id',
                'value'     => $model->company ? $model->company->name : null,
            ],
            [
                'attribute' => 'customer_id',
                'format'    => 'html',
                'value'     => $model->customer ? Html::a($model->customer->name,['/customer/view','id'=>$model->customer_id]) : null,
            ],
            'fee:currency',
            [
                'attribute' => 'purchase_id',
                'format'    => 'html',
                'value'     => Html::a(sprintf('%06d', $model->purchase_id),['/purchase/view','id'=>$model->purchase_id])
                    . Html::tag('p',implode('<br>', [
                        ArrayHelper::getValue($model,'purchase.create_date'),
                        Yii::$app->formatter->asCurrency(ArrayHelper::getValue($model,'purchase.total_charge')),
                        ArrayHelper::getValue($model,'purchase.branch.name'),
                        ArrayHelper::getValue($model,'purchase.note'),
                    ]), ['class'=>'help-block'])
            ],
            [
                'attribute' => 'create_date',
                'format'    => ['date','php:Y-m-d H:i'],
            ],
            [
                'attribute' => 'update_date',
                'format'    => ['date','php:Y-m-d H:i'],
            ],
        ],
    ]) ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => ChangeLog::find()->where(['tbl'=>$model->tableName(),'pkey'=>$model->commision_id]),
            'sort'  => ['defaultOrder' => ['create_date' => SORT_DESC]],
        ]),
        'caption' => 'DB操作履歴',
        'layout'  => '{items}{pager}',
        'showOnEmpty' => false,
        'tableOptions'   => ['class'=>'table table-condensed table-striped'],
        'summaryOptions' => ['class'=>'small text-right pull-right'],
        'columns' => [
            [
                'attribute' => 'create_date',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->create_date,['/change-log/view','id'=>$data->create_date]); }
            ],
            'route',
            'action',
            'user.name',
        ],
    ]) ?>

</div>
