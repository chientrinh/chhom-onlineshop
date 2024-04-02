<?php 
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/room/index.php $
 * $Id: index.php 3851 2018-04-24 09:07:27Z mori $
 *
 * $this \yii\web\View
 * $dataProvider
 * $searchModel
 *
 * $client (null || Customer)
 */

use \yii\helpers\Html;

$jscode = '
$("#toggle-btn").click(function(){
   $("#search-menu").toggle();
});
';
$this->registerJs($jscode);

$widget = \common\modules\sodan\widgets\InterviewGrid::begin([
    'dataProvider' => $dataProvider,
    'searchModel'  => $searchModel,
    'attributes'   => ['itv_id','branch_id','itv_date','itv_time','homoeopath_id'],
]);

$widget->columns[] = [
    'attribute' => 'client_id',
    'format'    => 'html',
    'value'     => function($data)
    {
        if($client = $data->client)
            return Html::a($data->client->name,Url::current(['Interview[client_id]'=>$data->client_id]),['class'=>'btn-default']) . Html::a('',['client/view','id'=>$data->client_id],['class'=>'glyphicon glyphicon-user']); 
        else
            return Html::a('指定',['search','target'=>'toranoko','id'=>$data->itv_id],['class'=>'btn btn-xs btn-primary'])
                       . ' '
                      .  Html::a('キャンセル待ちリストから指定',['search','target'=>'wait-list','id'=>$data->itv_id],['class'=>'btn btn-xs btn-warning']);
    }
];

?>

<div class="sodan-admin-index">

    <?=Html::button('絞り込み検索',['id'=>'toggle-btn','class'=>'btn btn-default']) ?>

    <div id="search-menu" class="well well-sm text-info" style="<?= count(Yii::$app->request->get()) ? null : 'display:none' ?>">
    <?= $this->render('search-menu',[
        'dateModel'   => $dateModel,
        'searchModel' => $searchModel,
    ]) ?>
    </div>

    <?= $widget->run() ?>

</div>
