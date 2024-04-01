<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/manufacture/index.php $
 * $Id: index.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchManufacture */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Manufactures';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="manufacture-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Manufacture', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'manufacture_id',
            'branch_id',
            'staff_id',
            'quantity',
            'craete_date',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
