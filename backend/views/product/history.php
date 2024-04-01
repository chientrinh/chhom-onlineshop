<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\models\ChangeLog;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product/history.php $
 * $Id: history.php 2727 2016-07-16 03:31:24Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Product
 */

if($company = $model->seller)
    $this->params['breadcrumbs'][] = [
        'label' => $company->name,
        'url' => ['index','company'=>$company->company_id]
    ];

$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view','id'=>$model->product_id]];
$this->params['breadcrumbs'][] = ['label' => 'DB操作履歴'];

?>

<div class="product-view-sales">

    <p class="pull-right">
        <?php if($model->prev): ?>
        <?= Html::a('', ['view', 'target' => 'sales', 'id' => $model->prev->product_id], ['title' => "前の商品",'class'=>'btn btn-xs btn-default glyphicon glyphicon-chevron-left']) ?>
        <?php endif ?>

        <?php if($model->next): ?>
        <?= Html::a("", ['view', 'target' => 'sales', 'id' => $model->next->product_id], ['title' => "次の商品",'class'=>'btn btn-xs btn-default glyphicon glyphicon-chevron-right']) ?>
        <?php endif ?>
    </p>

    <h1><?= Html::encode($model->name) ?></h1>

    <?= $this->render('_tab',['model'=>$model]) ?>

    <?= GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => ChangeLog::find()->where(['tbl'=>$model->tableName(),'pkey'=>$model->product_id]),
            'sort'  => ['defaultOrder' => ['create_date'=>SORT_DESC]],
        ]),
        'layout'  => '{pager}{items}',
        'caption' => 'DB操作履歴',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

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
