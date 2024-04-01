<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use miloschuman\highcharts\Highcharts;
use common\models\Branch;
use common\models\Company;
use common\models\Purchase;
use backend\models\PurchaseSurvey;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/purchase-survey/index.php $
 * $Id: index.php 2927 2016-10-06 06:14:16Z mori $
 *
 * @var $this  yii\web\View
 * @var $year  integer
 * @var $month integer
 * @var $query yii\db\ActiveQuery
 */

$this->params['breadcrumbs'][] = ['label' => "$year-$month", 'url' => ['index','year'=>$year,'month'=>$month]];

$matrix = [];
$days = range(1, date('t', strtotime("$year-$month-01")));
foreach($days as $day)
{
    $day = sprintf('%02d', $day);
    $ymd = implode('-', [$year,$month,$day]);

    $q1   = clone($query);
    $q1->andWhere(['like','target_date',"%$day", false]);
    $q2   = Purchase::find()->active()
                            ->andWhere(['EXTRACT(YEAR  FROM create_date)' => (int)$year])
                            ->andWhere(['EXTRACT(MONTH FROM create_date)' => (int)$month])
                            ->andWhere(['EXTRACT(DAY   FROM create_date)' => (int)$day]);

    if(strtotime(date('Y-m-d 00:00:00')) <= strtotime($ymd))
        $button = Html::tag('span','未来の日付です',['class'=>'text-muted']);
    elseif($q1->exists())
        $button = Html::a('',['view','date'=>$ymd],['class'=>'btn btn-xs btn-info glyphicon glyphicon-zoom-in','title'=>'閲覧します']);
    elseif(! $q2->exists())
        $button = Html::tag('span','対象なし',['class'=>'text-muted']);
    else
        $button = Html::a('',['create','date'=>$ymd],['class'=>'btn btn-xs btn-warning glyphicon glyphicon-pencil','title'=>'作成します']);

    $matrix[$day] = [
        'date'   => $ymd,
        'button' => $button,
    ];
}

$provider = new \yii\data\ArrayDataProvider([
    'allModels'  => $matrix,
    'pagination' => false,
]);

$prev = strtotime("$year-$month-01 -1 month");
$next = strtotime("$year-$month-01 +1 month");
?>

<p class="pull-right">
    <?= Html::a('更新',['update','year'=>$year,'month'=>$month],['class'=>'btn btn-success']) ?>
    <?= Html::a('',['index','year'=>date('Y',$prev), 'month'=>date('m', $prev)],['class'=>'btn btn-xs btn-default glyphicon glyphicon-chevron-left','title'=>date('Y-m', $prev)]) ?>
    <?= Html::a('',['index','year'=>date('Y',$next), 'month'=>date('m', $next)],['class'=>'btn btn-xs btn-default glyphicon glyphicon-chevron-right','title'=>date('Y-m', $next)]) ?>
</p>

<h2><?= $year ?> - <?= $month ?></h2>

<?php
$q3 = clone($query);
$q4 = clone($query);
$companies = Company::find()->where(['company_id' => $q3->select('company_id')->distinct()])
                            ->select(['key','company_id'])
                            ->indexBy('company_id')
                            ->column();

$branches  = Branch::find()->where(['branch_id' => $q4->select('branch_id')->distinct()])
                           ->select(['name','branch_id'])
                           ->indexBy('branch_id')
                           ->column();

$m1 = $matrix1 = []; // by Company
$m2 = $matrix2 = []; // by Branch

foreach($days as $day) // get records from table
{
    $q5 = clone($query);
    $q5->andWhere(['EXTRACT(DAY FROM target_date)' => $day]);

    foreach($q5->all() as $row)
    {
        $cid   = $row->company_id;
        $bid   = $row->branch_id;
        $v1 = ArrayHelper::getValue($m1, "$cid.$day", 0) + $row->sales;
        $v2 = ArrayHelper::getValue($m2, "$bid.$day", 0) + $row->sales;

        $m1[$cid][$day] = $v1;
        $m2[$bid][$day] = $v2;
    }
}

foreach($days as $day) // fill empty fields
{
    foreach($companies as $cid => $name)
    {
        if(! isset($m1[$cid][$day]))
            $m1[$cid][$day] = 0;
    }

    foreach($branches as $bid => $name)
    {
        if(! isset($m2[$bid][$day]))
            $m2[$bid][$day] = 0;
    }
}

foreach($companies as $cid => $name) // create highchart by Company
{
    $sales = $m1[$cid];
    ksort($sales);
    $sales = array_values($sales);

    $matrix1[] = ['name' => $name, 'data' => $sales, 'y' => array_sum($sales)];
}
foreach($branches as $bid => $name) // create highchart by Branch
{
    $sales = $m2[$bid];
    ksort($sales);
    $sales = array_values($sales);

    $matrix2[] = ['name' => $name, 'data' => $sales, 'y' => array_sum($sales)];
}

$matrix1[] = [
    'type' => 'pie',
    'name' => "総計",
    'data' => $matrix1,
    'center' => [50, -10],
    'size'         => 100,
    'showInLegend' => false,
    'dataLabels'   => [ 'enabled' => false ],
];

$matrix2[] = [
    'type' => 'pie',
    'name' => "総計",
    'data' => $matrix2,
    'center' => [50, -10],
    'size'         => 100,
    'showInLegend' => false,
    'dataLabels'   => [ 'enabled' => false ],
];


?>
<?= Highcharts::widget([
   'options' => [
      'title' => ['text' => "会社別"],
      'xAxis' => [
         'categories' => $days,
      ],
      'yAxis' => [
         'title' => ['text' => '売上高']
      ],
      'series' => $matrix1,
   ]
]) ?>

<?= Highcharts::widget([
   'options' => [
      'title' => ['text' => "拠点別"],
      'xAxis' => [
         'categories' => $days,
      ],
      'yAxis' => [
         'title' => ['text' => '売上高']
      ],
      'series' => $matrix2
   ]
]) ?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query'      => $query,
        'pagination' => false,
    ]),
    'filterModel' => $searchModel,
    'showFooter' => true,

    'columns' => [
        [
            'attribute'=>'target_date',
        ],
        [
            'attribute'=>'branch_id',
            'value'    => function($data){ return ($b = $data->branch) ? $b->name : null; },
            'filter'   => $branches,
        ],
        [
            'attribute'=>'company_id',  
            'value'    => function($data){ return ($c = $data->company) ? $c->key : null; },
            'filter'   => $companies,
            'contentOptions' => ['class'=>'text-uppercase'],
        ],
        [
            'attribute'=>'sales',
            'format'   =>'integer',
            'footer'   => number_format($query->sum('sales')),
            'contentOptions' => ['class'=>'text-right'],
            'footerOptions'  => ['class'=>'text-right'],
        ],
        [
            'attribute'=>'subtotal',
            'format'   =>'integer',
            'footer'   => number_format($query->sum('subtotal')),
            'contentOptions' => ['class'=>'text-right'],
            'footerOptions'  => ['class'=>'text-right'],
        ],
        [
            'attribute'=>'tax',
            'format'   =>'integer',
            'footer'   => number_format($query->sum('tax')),
            'contentOptions' => ['class'=>'text-right'],
            'footerOptions'  => ['class'=>'text-right'],
        ],
        [
            'attribute'=>'discount',
            'format'   =>'integer',
            'footer'   => number_format($query->sum('discount')),
            'contentOptions' => ['class'=>'text-right'],
            'footerOptions'  => ['class'=>'text-right'],
        ],
        [
            'attribute'=>'discount_item',
            'format'   =>'integer',
            'footer'   => number_format($query->sum('discount_item')),
            'contentOptions' => ['class'=>'text-right'],
            'footerOptions'  => ['class'=>'text-right'],
        ],
        [
            'attribute'=>'postage',
            'format'   =>'integer',
            'footer'   => number_format($query->sum('postage')),
            'contentOptions' => ['class'=>'text-right'],
            'footerOptions'  => ['class'=>'text-right'],
        ],
        [
            'attribute'=>'handling',
            'format'   =>'integer',
            'footer'   => number_format($query->sum('handling')),
            'contentOptions' => ['class'=>'text-right'],
            'footerOptions'  => ['class'=>'text-right'],
        ],
        [
            'attribute'=>'point_consume',
            'format'   =>'integer',
            'footer'   => number_format($query->sum('point_consume')),
            'contentOptions' => ['class'=>'text-right'],
            'footerOptions'  => ['class'=>'text-right'],
        ],
        [
            'attribute'=>'point_given',
            'format'   =>'integer',
            'footer'   => number_format($query->sum('point_given')),
            'contentOptions' => ['class'=>'text-right'],
            'footerOptions'  => ['class'=>'text-right'],
        ],
    ],
]) ?>
