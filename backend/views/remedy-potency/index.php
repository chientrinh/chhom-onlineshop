<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy-potency/index.php $
 * $Id: index.php 1552 2015-09-26 13:25:56Z mori $
 */

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SearchRemedyPotency */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Remedy Potencies';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="remedy-potency-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Remedy Potency', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'potency_id',
            'name',
            'weight',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}{update}',
            ],
        ],
    ]); ?>

</div>
