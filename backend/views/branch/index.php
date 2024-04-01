<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/branch/index.php $
 * $Id: index.php 4112 2019-02-06 06:26:37Z kawai $
 *
 * @var $this yii\web\View
 * @var $searchModel app\models\SearchBranch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = '拠点';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="branch-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Branch', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'branch_id',
                'value'     => function ($data) { return $data->branch_id; }
            ],
            [
                'attribute' => 'name',
                'format'    => 'raw',
                'value'     => function($data){ return Html::a($data->name, ['view', 'id'=>$data->branch_id]); },
            ],
            [
                'attribute' => 'addr',
                'value'     => function($data){ return $data->addr; },
            ],
            'tel',
            'email',
            [
                'attribute' => 'company_id',
                'format'    => 'raw',
                'value'     => function($data){ return Html::a($data->company->key, ['company/view', 'id'=>$data->company_id],['class'=>'text-uppercase']); },
                'filter'    => \yii\helpers\ArrayHelper::map(\common\models\Company::find()->all(), 'company_id', 'key')
            ],
        ],
    ]); ?>

</div>
