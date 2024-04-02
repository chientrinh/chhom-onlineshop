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

$fmt   = Yii::$app->formatter;
$venue = new EventVenue();

$css = "
.border-none th,
.border-none td
{
border: none;!important;
}
";

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

if($customer = Yii::$app->user->identity)
    $grade = $customer->grade;

$favor = new SearchProductFavor(['item' => $model, 'customer'=>Yii::$app->user->identity]);

?>

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

