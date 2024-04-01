<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/ysd/views/rrs/view.php $
 * @version $Id: view.php 1961 2016-01-11 01:39:26Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel  common\models\ysd\TransferRequest
 */

?>

<div class="transfer-response-index">

    <h1>振替結果</h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
            [
                'attribute' => 'trs_id',
                'format'    => 'html',
                'value'     => function($data)
                {
                    $label = sprintf('%06d', $data->trs_id);
                    return Html::a($label, ['view','id'=>$data->trs_id]);
                },
            ],
            'cdate',
            'rdate',
            'custno',
            'charge:currency',
            'pre:boolean',
            [
                'attribute' => 'stt',
                'value'     => function($data)
                {
                    return ($s = $data->status) ? $s->name : $data->stt;
                },
            ],
            ['attribute'=>'created_at','format'=>['datetime','php:Y-m-d H:i']],
        ],
        'rowOptions' => function ($model, $key, $index, $grid)
        {
            if(! $model->isPaid() )
                return ['class'=>'danger'];
        },
    ]); ?>
    
    <p>
        ※振替結果ファイル（CSV）を下のフォームで選択し「送信」をクリックすると、データを抽出して登録します
    </p>
    <?php echo $this->render('upload', ['model'=>$csvModel]);?>

</div>
