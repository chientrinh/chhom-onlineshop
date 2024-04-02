<?php

use yii\helpers\Html;
use yii\grid\GridView;
use \common\models\Branch;
use \yii\helpers\ArrayHelper;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/homoeopath/index.php $
 * $Id: index.php 3851 2018-04-24 09:07:27Z mori $
 *
 * @var $this \yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$labels = ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels); $labels[] = Yii::$app->name;

$this->title = implode(' | ', $labels);

$query  = Branch::find()->center();
$branches = ArrayHelper::map($query->all(),'branch_id','name');

$del_flg = common\models\sodan\Homoeopath::getDelFlg();
?>
<div class="homoeopath-index">

    <h1>ホメオパス</h1>
    <p class="help-block">
        現役ホメオパスを一覧表示しています
    </p>
    <p class="pull-right">
        <?= Html::a('追加', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{items}{pager}{summary}',
        'filterModel'  => $searchModel,
        'columns' => [
            [
                'attribute' => 'homoeopath_id',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->customer->homoeopathname, ['view','id'=>$data->homoeopath_id]); },
            ],
            [
                'attribute' => 'branch_id',
                'format'    => 'html',
                'value'     => function($data){ return $data->multibranchname; },
                'filter'    => $branches
            ],
            'schedule:ntext',
            [
                'label'     => '最近の相談会',
                'format'    => 'html',
                'value'     => function($data)
                {
                    $query = $data->getInterviews()
                                  ->past()
                                  ->active()
                                  ->orderBy(['itv_date'=>SORT_DESC,'itv_time'=>SORT_DESC]);

                    if(! $query->exists())
                        return;

                    $model = $query->one();

                    return Yii::$app->formatter->asDate($model->itv_date .' '. $model->itv_time, 'php:Y-m-d D H:i');
                }
            ],
            [
                'attribute' => 'del_flg',
                'format'    => 'html',
                'value'     => function($data){ return ($data->del_flg) ? '無効' : '有効'; },
                'filter'    => $del_flg
            ],
            [
                'attribute' => '',
                'format'    => 'html',
                'value'     => function($data)
                {
                    return Html::a('カレンダー表示',['calendar/index','hpath_id' => $data->homoeopath_id, 'branch_id' => $data->branch_id], ['class'=>'btn btn-xs btn-default']);
                }
            ],
        ],
    ]); ?>

</div>
