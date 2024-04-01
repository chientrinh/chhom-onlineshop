<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/webdb/views/liquor/view.php $
 * $Id: view.php 2445 2016-04-22 06:37:10Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use yii\helpers\Html;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\web\JsExpression;
use miloschuman\highcharts\Highcharts;

$rows   = $provider->allModels;
$date   = [];
$branch = [];
$map    = [];
foreach($rows as $row)
{
    $row = (object) $row;
    if(! in_array($row->date, $date))
        $date[] = $row->date;

    if(! in_array($row->center, $branch))
        $branch[] = $row->center;

    $map[$row->date][$row->center] = $row->ml;
}
sort($date);

$series['total'] = ['name'=>'total','type'=>'column','data'=> array(), 'y' => 0,
                    'color' => new JsExpression('Highcharts.getOptions().colors[0]')];
foreach($branch as $b)
{
    $series[$b]  = ['name'=>$b, 'data'=> array(), 'y' => 0,
    'color' => new JsExpression(sprintf('Highcharts.getOptions().colors[%d]', count($series)))];
}

foreach($date as $day)
{
    $row = $map[$day];

    $sum_of_day = 0;
    foreach($branch as $b)
    {
        if(array_key_exists($b, $row))
            $vol = (int) $row[$b];
        else
            $vol = 0;
        $sum_of_day += $vol;

        $series[$b]['data'][] = $vol;
    }
    $series['total']['data'][] = $sum_of_day;
}
foreach($series as $k => $v)
{
    $series[$k]['y'] = array_sum($series[$k]['data']);
}
$pie = [
    'type' => 'pie',
    'name' => "総計",
    'data' => array_values(array_slice($series, 1, count($series), false)),
    'center' => [50, -10],
    'size'         => 100,
    'showInLegend' => false,
    'dataLabels'   => [
    'enabled' => false,
        ],
    ];

$this->params['breadcrumbs'][] = ['label'=>'酒量の集計', 'url'=>['index']];
$this->params['breadcrumbs'][] = ['label'=>sprintf('%04d-%02d', $year, $month)];
?>
<div class="user-index">

    <p class="pull-right">
    <?= HTml::a('',['view',
                    'year' => ($month==1) ? $year - 1 : $year,
                    'month'=> ($month==1) ? 12        : $month - 1],
                ['class'=>'btn btn-sm btn-default glyphicon glyphicon-chevron-left',
                 'title'=>sprintf('%04d-%02d',($month==1)?$year-1:$year, ($month==1)?12:$month-1)
                ]) ?>
    <?= HTml::a('',['view',
                    'year' => ($month==12) ? $year + 1 : $year,
                    'month'=> ($month==12) ? 1         : $month + 1],
                ['class'=>'btn btn-sm btn-default glyphicon glyphicon-chevron-right',
                 'title'=>sprintf('%04d-%02d', ($month==12)?$year+1:$year, ($month==12)?1:$month+1)
                ]) ?>
    </p>

    <h1>酒量の集計 <small><?= $year ?>年<?= $month ?>月</small></h1>
    <p><?= date('Y-m-d H:i') ?> 現在</p>
    <p>
    <?= Html::a('戻る', ['index'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= Highcharts::widget([
   'options' => [
      'title' => ['text' => sprintf('%04d-%02d', $year, $month)],
      'xAxis' => [
          'categories' => $date,
      ],
      'yAxis' => [
         'title' => ['text' => 'ml']
      ],
      'series' => array_merge(array_values($series), array($pie)),
   ]
]); ?>

    <?= GridView::widget([
        'dataProvider' => new ArrayDataProvider([
            'allModels' => array_values($series),
            'sort' => [
                'attributes'   => ['name', 'y'],
                'defaultOrder' => ['y'=> SORT_ASC],
            ],
            'pagination' => false,
        ]),
        'caption' => "まとめ",
        'layout'  => '{items}',
        'columns' => [
            ['attribute'=> 'name',
             'label'    => "拠点",
             'format'   => 'text',
            ],
            ['attribute'=> 'y',
             'label'    => "総量(ml)",
             'format'   => 'integer',
             'contentOptions' => ['style'=>'text-align:right'],
            ],
        ],
    ]); ?>

    <?= GridView::widget([
        'dataProvider' => $provider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute'=> 'center',
             'label'    => "拠点",
             'format'   => 'text',
            ],
            'date',
            ['attribute'=> 'ml',
             'label'    => "容量(ml)",
             'format'   => 'integer',
             'contentOptions' => ['style'=>'text-align:right'],
            ],
        ],
    ]); ?>

</div>
