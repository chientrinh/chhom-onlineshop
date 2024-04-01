<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/zip/index.php $
 * $Id: index.php 2669 2016-07-07 08:39:18Z mori $
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ZipSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$pref = \common\models\Pref::find()->all();
$pref = ArrayHelper::map($pref, 'pref_id','name');

?>
<div class="zip-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p class="pull-right">
        <?= Html::a('CSV', ['csv'], ['class' => 'btn btn-default']) ?>
        <?= Html::a('一括編集', ['batch-update'], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('追加', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'region',
            'zipcode',
            [
                'attribute' => 'pref_id',
                'value'     => function($data){ return ($p = $data->pref) ? $p->name : null; },
                'filter'    => $pref,
            ],
            'city',
            'town',
            'yamato_22',
            'spat',

            ['class' => 'yii\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
