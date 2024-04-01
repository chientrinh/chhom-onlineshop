<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/role/index.php $
 * $Id: index.php 2337 2016-03-31 01:42:46Z mori $
 * @var $this yii\web\View
 * @var $searchModel backend\models\SearchRole
 * @var $dataProvider yii\data\ActiveDataProvider
 */

?>
<div class="role-index">

    <p class="pull-right">
        <?= Html::a('全体を一覧', ['/staff-role/index'], ['class' => 'btn btn-default']) ?>
    </p>

    <h1>役割</h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'role_id',
            [
                'attribute' => 'name',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->name, ['view','id'=>$data->role_id]); }
            ],
            'description',
        ],
    ]); ?>

</div>
