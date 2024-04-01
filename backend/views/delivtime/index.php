<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/delivtime/index.php $
 * $Id: index.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchDelivTime */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Deliv Times';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="deliv-time-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Deliv Time', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'dtime_id:datetime',
            'deliveror_id',
            'time',
            'name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
