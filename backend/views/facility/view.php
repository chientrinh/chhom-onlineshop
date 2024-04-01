<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/facility/view.php $
 * $Id: view.php 3987 2018-08-17 02:30:40Z mori $
 *
 * $provider: DataProvider
 */

use \yii\helpers\ArrayHelper;
use \yii\helpers\Html;
use \yii\helpers\Url;
use \common\models\ChangeLog;
use \common\models\CustomerMembership;

?>
<div class="facility-view">

    <p class="pull-right">
        <?= Html::a('編集',['update','id'=>$model->facility_id],['class'=>'btn btn-primary']) ?>
    </p>
    <h1>
        <?=ArrayHelper::getValue($model,'name') ?>
    </h1>

    <div class="row">
    <div class="col-md-8">
    <?= \yii\widgets\DetailView::widget([
        'model'=>$model,
        'attributes' => [
            'facility_id',
            [
                'attribute' => 'customer_id',
                'format'    => 'html',
                'value'     => Html::a($model->customer_id, ['/customer/view','id'=>$model->customer_id]),
            ],
            'name',
            'title',
            'email',
            [
                'attribute' => 'url',
                'format'    => 'raw',
                'value'     => $model->url ? Html::a($model->url, $model->url, ['class'=>'glyphicon glyphicon-new-window','target'=>'_blank']) : null,
            ],
            [
                'attribute' => 'pub_date',
                'format'    => 'html',
                'value'     => $model->private
                             ? Html::tag('del', $model->pub_date)
                             : $model->pub_date,
            ],
            'private:boolean',
            [
                'attribute' => 'update_date',
                'format'    => ['date','php:Y-m-d H:i:s'],
            ],
        ],
    ]) ?>
    </div>
    <div class="col-md-4">
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getMemberships()
                       ->active(),
            'pagination' => false,
        ]),
        'layout' => '{items}',
        'columns' => [
            [
                'header'    => '提携施設',
                'attribute' => 'membership.name',
            ],
        ],
    ]) ?>
    </div>
    </div>

    <p><strong><?= $model->getAttributeLabel('addr') ?></strong></p>
    <?= \yii\widgets\DetailView::widget([
        'model'=>$model,
        'options' => ['class' => 'table table-condenced'],
        'attributes' => [
            'zip',
            'addr',
            'tel',
            'fax',
        ],
    ]) ?>

    <p><strong><?= $model->getAttributeLabel('summary') ?></strong></p>
    <div class="well">
        <?= nl2br($model->summary) ?>
    </div>

    <div class="col-md-12">
        <h3><small>DB操作履歴</small>
        </h3>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => ChangeLog::find()->where(['tbl'=>$model->tableName(), 'pkey'=>$model->customer_id]),
            'sort'  => ['defaultOrder' => ['create_date' => SORT_DESC]],
        ]),
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
        ]); ?>
    </div>

</div>
