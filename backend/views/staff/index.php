<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/staff/index.php $
 * $Id: index.php 2238 2016-03-12 07:50:04Z mori $
 */

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchStaff */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = "従業員";
$this->params['breadcrumbs'][] = $this->title;

$companies = \yii\helpers\ArrayHelper::map(\common\models\Company::find()->all(), 'company_id', 'name');
?>
<div class="staff-index">

    <p class="pull-right">
        <?= Html::a("追加", ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'filter'   => $companies,
                'attribute'=>'company_id',
                'format'   =>'raw',
                'value'    =>function($data){ return $data->company->name; },
            ],
            [
                'attribute'=>'name',
                'format'   =>'raw',
                'value'    =>function($data){ return Html::a($data->name, ['view','id'=>$data->staff_id]); },
            ],
            'email',
            'update_date',

        ],
    ]); ?>

</div>
