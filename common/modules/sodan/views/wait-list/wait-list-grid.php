<?php

use \yii\helpers\Html;
use \yii\helpers\ArrayHelper;

/**
 * $URL: https://localhost:44344/svn/MALL/common/modules/sodan/views/client/update.php $
 * $Id: update.php 1853 2015-12-09 11:06:24Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider
 * @var $searchModel common\models\sodan\WaitList
 */

$intraRequest = null;
if($cookie = Yii::$app->request->cookies->get('ebisu-intra-request-json'))
    $intraRequest = \yii\helpers\Json::decode($cookie);

$query = \common\models\sodan\Homoeopath::find()->active();
if (isset($searchModel->branch_id) && $branch_id = $searchModel->branch_id) {
    $query->multibranch($branch_id);
}
$hpath = ArrayHelper::map($query->all(),'homoeopath_id','customer.homoeopathname');
?>

<?= \yii\grid\GridView::widget([
    'tableOptions' => ['class'=>'table table-condensed table-striped'],
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'showOnEmpty'  => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'wait_id',
                'format'    => 'html',
                'value'     => function($data){
                    return Html::a(sprintf('%06d',$data->wait_id),['view','id'=>$data->wait_id]);
                },
            ],
            [
                'attribute' => 'client_id',
                'format'    => 'html',
                'value'     => function($data){
                    if($c = $data->client)
                        return Html::a($c->name,['client/view','id'=>$data->client_id],['title'=>$c->kana]);
                },
                'filterInputOptions'=>['class'=>'form-control','placeholder'=>'氏名、かな、kana'],
            ],
            [
                'attribute' => 'branch_id',
                'format'    => 'html',
                'value'     => function($data){
                    if($data->branch)
                        return $data->branch->name;
                },
                'filter'    => ArrayHelper::map(\common\models\Branch::find()->center()->all(),'branch_id','name'),
            ],
            [
                'attribute' => 'itv_date',
                'format'    => 'html',
                'value'     => function($data){
                    if($data->interview)
                        return $data->interview->itv_date;
                },
                'filter'    => false
            ],
            [
                'attribute' => 'homoeopath_id',
                'format'    => 'html',
                'value'     => function($data){
                    if ($data->interview)
                        return Html::a($data->interview->homoeopath->homoeopathname, ['homoeopath/view', 'id' => $data->interview->homoeopath_id]);
                    else if($data->homoeopath)
                        return Html::a($data->homoeopath->homoeopathname, ['homoeopath/view', 'id' => $data->homoeopath_id]);
                },
                'filter'    => $hpath,
            ],
            'note',
            [
                'attribute'=>'expire_date',
                'format'   =>'date',
            ],
            [
                'label'  => '',
                'format' => 'raw',
                'value'  => function($data)use($intraRequest)
                {
                    if(! $intraRequest) return null;

                    $url = $intraRequest['route'] . sprintf('&customer_id=%d',$data->client_id);
                    return Html::a("予約",$url,['class'=>'btn btn-xs btn-danger','title'=>$intraRequest['title']]);
                },
                'visible' => $intraRequest,
            ],
        ],
    ]); ?>
