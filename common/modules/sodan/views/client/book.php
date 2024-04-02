<?php

use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;
use common\models\sodan\Homoeopath;
use \common\modules\sodan\widgets\InterviewGrid;
use yii\helpers\Url;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/client/book.php $
 * $Id: book.php 3851 2018-04-24 09:07:27Z mori $
 *
 * $this \yii\web\View
 * $dataProvider
 * $searchModel
 */

$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view','id'=>$model->client_id]];
$this->params['breadcrumbs'][] = ['label' => '予約', 'url' => ['book','id'=>$model->client_id] ];

$jscode = '
$("#toggle-btn").click(function(){
   $("#search-menu").toggle();
});
';
$this->registerJs($jscode);

// get Array of Homoeopaths
$query = Homoeopath::find()->with('customer')->active()->multibranch($branch_id);
$hpaths = ArrayHelper::map($query->all(),'homoeopath_id','customer.homoeopathname');
ksort($hpaths);

$w1 = InterviewGrid::begin([
    'dataProvider' => $dataProvider,
    'searchModel'  => $searchModel,
    'attributes'   => [
        'itv_id',
        'branch_id',
        [
            'attribute' => 'itv_date',
            'format' => ['date','php:Y-m-d(D)'],
        ],
        'itv_time',
        [
            'attribute' => 'homoeopath_id',
            'format'    => 'html',
            'value'     => function($data) { return ($h = $data->homoeopath) ? $h->homoeopathname : null ; },
            'filter'    => $hpaths,
        ],
        [
            'format'    => 'raw',
            'value'     => function($data) use($model) {
                return Html::a('予約',['room/book', 'client_id' => $model->client_id, 'itv_id' => $data->itv_id], ['class' => 'btn btn-xs btn-warning']);
            }
        ]
    ],
]);

$w2 = InterviewGrid::begin([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query'      => $model->getInterviews()->past()
                              ->limit(5)
                              ->orderBy(['itv_date'=>SORT_DESC,'itv_time'=>SORT_DESC]),
        'pagination' => false,
        'sort'       => false,
    ]),
    'attributes'   => [
        'branch_id',
        [
            'attribute' => 'itv_date',
            'format' => ['date','php:Y-m-d(D)'],
        ],
        'itv_time',
        'homoeopath_id',
    ],
]);

$w3 = InterviewGrid::begin([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query'      => $model->getInterviews()->future(),
        'pagination' => false,
        'sort'       => false,
    ]),
    'attributes'   => [
        'branch_id',
        [
            'attribute' => 'itv_date',
            'format' => ['date','php:Y-m-d(D)'],
        ],
        'itv_time',
        'homoeopath_id',
    ],
]);

?>

<div class="sodan-client-book col-md-12">

    <h2>
        <?= $model->name ?>
    </h2>

    <div class="col-md-6">
        <h3 class="text-muted">
            最近の履歴
        </h3>
        <?= $w2->run() ?>
    </div>

    <div class="col-md-6">
        <h3 class="text-muted">
            予約済み
        </h3>
        <?= $w3->run() ?>
    </div>

    <div class="col-md-12">
        <h3 class="text-muted">
            予約可能な日時
        </h3>
        <?= $w1->run() ?>
    </div>

</div>
