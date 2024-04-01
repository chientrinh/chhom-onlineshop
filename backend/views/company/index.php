<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/company/index.php $
 * $Id: index.php 2337 2016-03-31 01:42:46Z mori $
 *
 * @var $this yii\web\View
 * @var $searchModel app\models\SearchCompany
 * @var $dataProvider yii\data\ActiveDataProvider
 */

?>
<div class="company-index">

    <p class="pull-right">
        <?= Html::a('追加', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <h1>会社</h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'company_id',
            [
                'attribute' => 'key',
                'contentOptions' => ['class'=>'text-uppercase'],
            ],
            [
                'attribute'=> 'name',
                'format'   => 'raw',
                'value'    => function($data){ return Html::a($data->name, ['view', 'id'=>$data->company_id]); },
            ],
            'manager',
            'email',
            'zip',
            'addr',
        ],
    ]); ?>

</div>
