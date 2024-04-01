<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/storage/index.php $
 * $Id: index.php 804 2015-03-19 07:31:58Z mori $
 */

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchStorage */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Storages';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="storage-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Storage', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'storage_id',
            'src_id',
            'dst_id',
            'staff_id',
            'ship_date',
            // 'pick_date',
            // 'create_date',
            // 'update_date',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
