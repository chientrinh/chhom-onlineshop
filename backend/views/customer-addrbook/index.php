<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/customer-addrbook/index.php $
 * $Id: index.php 2994 2016-10-20 05:03:22Z mori $
 */

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SearchCustomerAddrbook */
/* @var $dataProvider yii\data\ActiveDataProvider */

$title = 'お届け先';
$this->title = sprintf('%s | 顧客 | %s', $title, Yii::$app->name);
$this->params['breadcrumbs'][] = ['label' => '顧客', 'url' => ['/customer/index']];
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => '一覧', 'url' => ['index']];
?>
<div class="customer-addrbook-index">

    <h1><?= Html::encode($title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'customer_id',
                'format'    => 'html',
                'value'     => function($data)
                {
                    if($c = $data->customer)
                        return Html::a($c->name, ['/customer/view','id'=>$c->customer_id]);
                },
            ],
            'name',
            'kana',
            'zip',
            'addr',
            'tel',
            'update_date:date',
            [
                'class'    => yii\grid\ActionColumn::className(),
                'template' => '{update}',
            ],
        ],
    ]); ?>

</div>
