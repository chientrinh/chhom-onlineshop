<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use common\models\EventVenue;
use common\models\SearchProductFavor;

/**
 * @var $this yii\web\View
 * @var $model  common\models\Product
 * @var $provider yii\data\ActiveDataProvider
 */

$this->params['body_id'] = 'Product';
$this->params['breadcrumbs'][] = ['label' => 'イベント', 'url' => ['/category/viewbyname','name'=>'イベント']];

$fmt   = Yii::$app->formatter;
$venue = new EventVenue();

$finder = new SearchProductFavor([
    'item'    => $model,
    'customer'=> Yii::$app->user->identity,
]);

$css = "
.border-none th,
.border-none td
{
border: none;!important;
}
";
$this->registerCss($css);
$jscode = "
  $(document).ready(function(){
    $('.bxslider').bxSlider({
      infiniteLoop: true,
      hideControlOnEnd: true,
      speed: 500,
      useCSS: false,
      controls: true,
      captions: true
    });
  });
";
$this->registerJs($jscode, \yii\web\View::POS_LOAD);
$this->registerJsFile('@web/js/flexslider/jquery.bxslider.js', ['depends'=>['yii\web\YiiAsset','yii\bootstrap\BootstrapAsset']]);

if(1 == $model->getVenues()->select('event_date')->distinct()->count())
    $event_date = $fmt->asDate($model->getVenues()->select('event_date')->scalar(), 'php:Y-m-d (D)');
else
    $event_date = "下記参照";

if(1 == $model->getVenues()->select('start_time')->distinct()->count())
    $start_time = $model->getVenues()->select('start_time')->scalar();
if(1 == $model->getVenues()->select('end_time')->distinct()->count())
    $end_time = $model->getVenues()->select('end_time')->scalar();

if(isset($start_time) && isset($end_time))
    $event_time = $fmt->asTime($start_time, 'php:H:i')
                . ' - '
                . $fmt->asTime($end_time, 'php:H:i');
else
    $event_time = "下記参照";

// prepare images
$slider = [];
$pager  = [];
if(! $model->images)
{
    $img_src  = Url::to('@web/img/default.jpg');
    $slider[] = sprintf('<li><img src="%s" alt="%s" style="max-width:270px"></li>', $img_src, $model->name);
    $pager[]  = sprintf('<span><a data-slide-index="%d" href=""><img src="%s" alt="%s" ></a></span>', count($pager), $img_src, $model->name);
}
else foreach($model->images as $image)
{
    $slider[] = sprintf('<li><a href="%s"><img src="%s" alt="%s" class="btn" style="max-width:270px"></a></li>', Url::to(['view-image','id'=>$model->product_id,'top'=>$image->basename]), $image->url, $model->name);
    $pager[]  = sprintf('<span><a data-slide-index="%d" href=""><img src="%s" alt="%s" ></a></span>', count($pager), $image->url, $model->name);
}

if($customer = Yii::$app->user->identity)
{
    $grade = $customer->grade;
}
$favor = new SearchProductFavor(['item' => $model, 'customer'=>Yii::$app->user->identity]);

?>
<div class="event-venue-view">

<div class="col-md-4 product-photo">

    <ul class="bxslider">
    <?= implode('', $slider) ?>
    </ul>
    <div id="bx-pager">
    <?= implode('', $pager) ?>
    </div>

</div>
 
<div class="col-md-8 product-detail">

<h3>
<span class="Shop"><?= Html::a($model->company->name,['/'.$model->company->key],['style'=>'color:#999']) ?></span>
<span class="Mame"><?= $model->name ?></span>
</h3>

    <?= yii\widgets\DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table-condenced'],
        'attributes' => [
            [
                'attribute' => "開催日：",
                'value'     => $event_date,
            ],
            [
                'label'     => "開催時刻：",
                'value'     => $event_time,
            ],
            [
                'attribute' => "参加費：",
                'format'    => 'html',
                'value'     => Html::tag('em', $fmt->asCurrency($model->price),['style'=>'font-size: 120%;font-weight: bold;']) . "（税別）",
            ],
            [
                'label'     => 'ご優待：',
                'format'    => 'html',
                'value'     => Html::tag('span', $fmt->asInteger($favor->discount->rate) . "%", ['class'=>'text-info']),
                'visible'   => $favor->discount->rate,
            ],
            [
                'label'     => 'ポイント：',
                'format'    => 'html',
                'value'     => Html::tag('span', $fmt->asInteger($favor->point->rate) . "%", ['class'=>'text-info']),
                'visible'   => $favor->point->rate,
            ],
        ],
    ]) ?>

   <p>
       <br>
       <?= $fmt->asHtml($model->description) ?>
       <br>
   </p>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getVenues(),
            'sort'  => false,
        ]),
        'layout' => '{items}',
        'tableOptions' => ['class'=>'table table-condensed table-hover'],
        'columns' => [
            [
                'attribute' => 'event_date',
                'value'     => function($data){
                    return Yii::$app->formatter->asDate($data->event_date, 'php:m/d(D)');
                },
                'visible'   => (1 < $model->getVenues()->select('event_date')->distinct()->count())
            ],
            [
                'attribute' => 'start_time',
                'visible'   => (1 < $model->getVenues()->select('start_time')->distinct()->count())
            ],
            [
                'attribute' => 'end_time',
                'visible'   => (1 < $model->getVenues()->select('end_time')->distinct()->count())
            ],
            [
                'attribute' => 'name',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->name,['apply','id'=>$data->venue_id]); },
            ],
            'capacity',
            [
                'attribute'  => 'vacancy',
                'value'  => function($data){
                    if($data->occupancy <   0.5) return '◎';
                    if($data->vacancy   <= 20  ) return '▲';
                    return '○';
                },
            ],
            [
                'attribute' => 'allow_child',
                'value'     => function($data){return $data->allow_child ? '○' : '×'; }
            ],
            [
                'label'=>'',
                'format'=>'html',
                'value'=>function($data)
                {
                    return Html::a('予約',['apply','id'=>$data->venue_id],['class'=>'btn btn-warning btn-sm']);
                },
            ],
        ],
    ]) ?>

満席の会場へ参加をご希望の方は各会場の代表TELへご連絡のうえ、キャンセル待ちをお申し付けください。
</div>
</div>
