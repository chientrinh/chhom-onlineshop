<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/webdb/views/history/view.php $
 * $Id: view.php 3195 2017-02-19 08:01:42Z naito $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$title = $code;
$this->title = sprintf(' %s | 販売履歴 | webdb18 | %s ', $code, Yii::$app->name);
$this->params['breadcrumbs'][] = 'webdb18';
$this->params['breadcrumbs'][] = ['label' => '販売履歴', 'url' => ['index']];
$this->params['breadcrumbs'][] = $code;

if($dataProvider->models)
{
    $model = $dataProvider->models[0];
    $name  = $model->d_item_1_syohin_name_hidden;
}
?>

<div class="webdb-liquor-index">
    <h1>
        <?= isset($name) ? $name : null ?>
        <small>
        <?= $title ?>
        </small>
    </h1>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'caption' => $code . "の販売履歴",
        //'layout'  => '{summary}{items}{pager}',
        'columns' => [
            'd_item_date',
            'denpyo.denpyo_time',
            [
                'attribute' => 'denpyo_num',
                'format'    => 'raw',
                'value'     => function($data)
                {
                    return sprintf("<a href='%s' class='glyphicon glyphicon-log-out' target='webdb'> %s%s</a>",
                                   'https://webdb18.homoeopathy.co.jp/index.php?' .
                                   implode('&',['m=denpyo_in',
                                                'out_html=denpyo_chk',
                                                "customerid=".$data->customerid,
                                                "denpyo_num=".$data->denpyo_num,
                                                "denpyo_num_division={$data->denpyo_num_division}"]),
                                   $data->denpyo_num_division,
                                   $data->denpyo_num);
                },
                'filterInputOptions' => ['placeholder'=>'数字のみ入力可','class'=>'form-control'],
            ],
            'denpyo.center.denpyo_center',
            [
                'attribute' => 'd_item_2_division',
            ],
        ],
    ]); ?>

</div>
