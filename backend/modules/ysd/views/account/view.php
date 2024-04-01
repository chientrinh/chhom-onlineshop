<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/ysd/views/account/view.php $
 * $Id: view.php 3103 2016-11-24 05:38:13Z mori $
 *
 * $model \common\ysd\Account
 *
 */
use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;
use \common\models\ChangeLog;
use \common\models\ysd\RegisterRequest;
use \common\models\ysd\RegisterResponse;
use \common\models\ysd\TransferResponse;

$this->params['breadcrumbs'][] = ['label' => ($c = $model->customer) ? $c->name : null];

?>

<h1><?= ArrayHelper::getValue($model, 'customer.name') ?></h1>

<?= \yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'attribute' => 'customer_id',
            'format'    => 'html',
            'value'     => Html::a($model->customer_id, ['/customer/view','id'=>$model->customer_id]),
        ],
        [
            'attribute' => 'expire_id',
            'value'     => ArrayHelper::getValue($model, 'expire.name'),
        ],
        'credit_limit:currency',
        'create_date',
        'update_date',
    ],
]) ?>

<div class="text-right">
<?= Html::a('編集',['update','id'=>$model->customer_id],['class'=>'btn btn-primary']) ?>
</div>

<h3><small>振替結果</small></h3>

<?= \yii\grid\GridView::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => TransferResponse::find()->where(['custno' => $model->customer_id]),
        'sort'  => ['defaultOrder'=>['created_at'=>SORT_DESC]],
        'pagination'=>['pageSize'=>5],
    ])
]) ?>

<h3><small>登録結果</small></h3>

<?= \yii\grid\GridView::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => RegisterResponse::find()->where(['custno' => $model->customer_id]),
        'sort'  => ['defaultOrder'=>['created_at'=>SORT_DESC]],
        'pagination'=>['pageSize'=>5],
    ]),
    'columns' => [
        [
            'attribute' => 'rrs_id',
            'format'    => 'html',
            'value'     => function($data){ return Html::a(sprintf('%04d',$data->rrs_id), ['rrs/view','id'=>$data->rrs_id]); },
        ],
        'cdate',
        'custkana',
        'custname',
        'errcd',
        'created_at:date',
    ],
]) ?>

<h3><small>DB操作履歴</small></h3>

<?= \yii\grid\GridView::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => ChangeLog::find()->where(['tbl' => $model->tableName(),
                                             'pkey'=> $model->customer_id]),
    ]),
    'columns' => [
        [
            'attribute' => 'create_date',
            'format'    => 'html',
            'value'     => function($data){ return Html::a($data->create_date,['/change-log/view','id'=>$data->create_date]); },
        ],
        'route',
        'action',
        'user.name',
    ],
]) ?>

