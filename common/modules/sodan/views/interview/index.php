<?php

use \yii\helpers\Html;
use common\models\sodan\InterviewStatus;
use yii\helpers\ArrayHelper;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/interview/index.php $
 * $Id: index.php 4142 2019-03-28 08:39:08Z kawai $
 *
 * $this \yii\web\View
 * $dataProvider
 * $searchModel
 */

$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels);
$this->title = implode(' | ', $labels) . ' | '. Yii::$app->name;
$time = Yii::$app->request->get('time');

if('-1' == $time)
    $dataProvider->sort->defaultOrder = ['itv_date'=>SORT_DESC,'itv_time'=>SORT_DESC];
else
    $dataProvider->sort->defaultOrder = ['itv_date'=>SORT_ASC, 'itv_time'=>SORT_ASC ];

$query = common\models\sodan\Homoeopath::find()->active();
if ($branch_id) {
    $query->multibranch($branch_id);
}
$homoeopath = ArrayHelper::map($query->all(), 'homoeopath_id', 'customer.homoeopathname');
?>

<div class="sodan-admin-index">

<?php if(0 <= $time && Yii::$app->id === 'app-backend'): ?>
    <p class="pull-right">
        <?= Html::a('追加', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php endif ?>
<?php
    $calendar_url = is_null($branch_id) ? 'calendar/index' : "calendar/index?branch_id={$branch_id}";
?>
<p class="pull-right">
    <?= Html::a('カレンダー表示', [$calendar_url], ['class' => 'btn']) ?>
</p>

    <?= \yii\bootstrap\Nav::widget([
        'options' => ['class'=>"nav nav-tabs"],
        'items' => [
            ['label'=>'すべて','url'=>['index','time'=> null], 'active'=> (null === $time)],
            ['label'=>'過去', 'url'=>['index','time'=>-1], 'active'=> ('-1' == $time)],
            ['label'=>'本日', 'url'=>['index','time'=> 0], 'active'=> ('0'  == $time)],
            ['label'=>'予定', 'url'=>['index','time'=> 1], 'active'=> ('1'  == $time)],
        ],
    ]) ?>

    <?= \common\modules\sodan\widgets\InterviewGrid::widget([
        'dataProvider' => $dataProvider,
        'searchModel'  => $searchModel,
        'homoeopath'   => $homoeopath,
        'attributes'   => ['itv_id', 'branch_id', 'itv_date', 'itv_time', 'duration', 'homoeopath_id', 'client_id', 'product_id',
                           [
                               'attribute' => 'officer_use',
                               'value'     => function($data)
                               {
                                   if(! $data->officer_use)
                                       return '';

                                   return \yii\helpers\StringHelper::truncate($data->officer_use, 8);
                               },
                               'visible' => Yii::$app->id === 'app-backend'
                           ],
                           'status_id',
                           [
                               'attribute' => 'purchase_id',
                               'format'    => 'raw',
                               'value'     => function($data)
                               {
                                   if($data->purchase && 'app-backend' === Yii::$app->id) {
                                       $html = Html::a(sprintf('%06d', $data->purchase_id),['purchase/view','id'=>$data->itv_id]);
                                       // チケット会計チェック
                                       if ($data->ticket_id) {
                                           $html .= "<br>※チケット会計 【" . sprintf('%05d', $data->ticket_id) . '】';
                                       }
                                       return $html;
                                   }

                                   $purchase_flg = ($data->status_id == InterviewStatus::PKEY_READY || $data->status_id == InterviewStatus::PKEY_DONE || $data->status_id == InterviewStatus::PKEY_KARUTE_DONE || $data->status_id == InterviewStatus::PKEY_CANCEL) ? true : false;
                                   if($data->product && 'app-backend' === Yii::$app->id && $purchase_flg) {
                                       $html = Html::a('起票',['purchase/create','id'=>$data->itv_id],['class'=>'btn btn-xs btn-primary']);
                                       // チケット会計チェック
                                       if ($data->ticket_id) {
                                           $html .= "<br>※チケット会計 【" . sprintf('%05d', $data->ticket_id) . "】";
                                       }
                                       return $html;
                                   }
                                   return '';
                               },
                               'visible' => Yii::$app->id === 'app-backend'
                           ],
        ],
    ]); ?>
</div>
