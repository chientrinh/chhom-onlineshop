<?php 

use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/stat/index.php $
 * $Id: index.php 3851 2018-04-24 09:07:27Z mori $
 *
 * $this \yii\web\View
 * $model \common\models\statistics\SodanStatistic
 */

$title = sprintf('健康相談 集計 %d 年 %02d 月', $model->year, $model->month);

$widget = \yii\grid\GridView::begin([
    'dataProvider' => new \yii\data\ArrayDataProvider(),
    'columns' => [
        [
            'label'     => '名前',
            'format'    => 'html',
            'value'     => function($data)use($model)
            {
                $name = ArrayHelper::getValue($data,'name');

                if($value = ArrayHelper::getValue($data,'homoeopath_id'))
                    return Html::a($name,['view','id'=>$value,'target'=>'hpath','year'=>$model->year,'month'=>$model->month]);

                if($value = \yii\helpers\ArrayHelper::getValue($data,'branch_id'))
                    return Html::a($name,['view','id'=>$value,'target'=>'branch','year'=>$model->year,'month'=>$model->month]);
            },
            'headerOptions' => ['class'=>'col-md-3'],
        ],
        [
            'attribute' => 'interview',
            'label'     => $model->getAttributeLabel('interview'),
            'contentOptions' => ['class'=>'text-right'],
        ],
        [
            'attribute' => 'purchase',
            'label'     => $model->getAttributeLabel('purchase'),
            'format'    => 'html',
            'value'     => function($data){
                $purchase  = ArrayHelper::getValue($data,'purchase');
                $interview = ArrayHelper::getValue($data,'interview');

                $html = $purchase;
                if($purchase != $interview)
                    $html = Html::tag('span',$html,['class'=>'not-set']);

                return $html;
            },
            'contentOptions' => ['class'=>'text-right'],
        ],
        [
            'attribute' => 'sales',
            'label'     => $model->getAttributeLabel('sales'),
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
        ],
        [
            'attribute' => 'commission',
            'label'     => $model->getAttributeLabel('commission'),
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
        ],
    ],
]);


$rows   = $model->find()->orderBy('i.itv_date ASC')->select([
    'b.name as branch',
    'concat(h.name01,h.name02) as hpath',
    'concat(l.name01,l.name02) as client',
    'i.itv_id',
    'i.itv_date',
    'i.itv_time',
    's.name as status',
    'p.purchase_id',
    'p.subtotal',
    'c.commision_id',
    'c.fee',
])->all();
$days   = array_unique(ArrayHelper::getColumn($rows,'itv_date'));
$matrix = [];

$i = 0;
foreach(ArrayHelper::getColumn($rows,'branch') as $branch)
{
    $key = $branch;
    if(! isset($matrix[$key]))
        $matrix[$key] = [
            'name' => $branch,
            'data' => [],
            'color'=> new \yii\web\JsExpression(sprintf('Highcharts.getOptions().colors[%d]', $i++)),
        ];

    foreach($days as $day)
        $matrix[$key]['data'][$day] = 0;
}
$row = end($matrix);
$row['name']  = 'total';
$row['color'] = new \yii\web\JsExpression(sprintf('Highcharts.getOptions().colors[%d]', $i));
$row['type']  = 'column';
$matrix = array_merge(['total'=>$row], $matrix);

foreach($rows as $row)
{
    $key = $row['branch'];
    $day = $row['itv_date'];
    $stt = $row['subtotal'];

    $matrix[$key]['data'][$day]    += (int)$stt;
    $matrix['total']['data'][$day] += (int)$stt;
}
$series = [];
foreach($matrix as $row)
{
    if(isset($row['data']))
        $row['data'] = array_values($row['data']);

    $series[] = $row;
}
$pie = [
    'type' => 'pie',
    'name' => "総計",
    'data' => [],
    'center' => [50, 0],
    'size'         => 100,
    'showInLegend' => false,
    'dataLabels'   => [
        'enabled' => true,
    ],
];
foreach($series as $row)
{
    if('total' == $row['name'])
        continue;

    $pie['data'][] = [
        'name' => $row['name'],
        'data' => $row['data'],
        'y'    => array_sum($row['data']),
        'color'=> $row['color'],
    ];
}
$series[] = $pie;
?>

<div class="sodan-stat-index col-md-12">

    <?php if($model->hasErrors()): ?>
    <?= Html::errorSummary($model,['class'=>'alert alert-warning']) ?>
    <?php endif ?>

    <?= \miloschuman\highcharts\Highcharts::widget([
        'options' => [
            'title' => ['text' => $title],
            'xAxis' => [
                'categories' => array_values($days),
            ],
            'yAxis' => [
                'title' => ['text' => '売上 (円)']
            ],
            'series' => $series,
        ]
    ]) ?>

    <h3>拠点ごと</h3>
    <?php $widget->dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $model->getRows('branch_id')
    ]); ?>
    <?= $widget->run() ?>

    <h3>ホメオパスごと</h3>
    <?php
    $widget->dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $model->getRows('homoeopath_id')
    ]); ?>
    <?= $widget->run() ?>

    <h3>明細</h3>
    <?= $this->render('detail-grid',[
        'model'        => $model,
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->find(),
        ]),
    ]) ?>

    <div class="col-md-3">
        <div class="panel panel-success">
            <div class="panel-heading">
                任意の年月で集計
            </div>
            <div class="panel-body">
                <?php $form = \yii\bootstrap\ActiveForm::begin([
                    'action' => ['index'],
                    'method' => 'get',
                ]); ?>
                <?= $form->field($model, 'year')->textInput(['name'=>'year']) ?>
                <?= $form->field($model, 'month')->textInput(['name'=>'month']) ?>
                <?= Html::submitbutton('表示',['class'=>'btn btn-success']) ?>
                <?php $form->end() ?>
            </div>
        </div>
    </div>

</div>
