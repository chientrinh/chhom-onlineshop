<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/webdb/views/history/index.php $
 * $Id: index.php 3194 2017-02-19 07:36:40Z naito $
 */

use yii\helpers\Html;
$title = '販売履歴';
$this->title = '販売履歴 | webdb18 | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = 'webdb18';
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['index']];

$dataProvider->query->with('denpyo.center');
?>

<div class="webdb-liquor-index">
    <h1><?= $title ?></h1>

    <form action="<?= \yii\helpers\Url::toRoute('/webdb/history/view') ?>" method="get">

    <p>
        <label for="code">商品CODE</label>
        <?= Html::textInput('code', null, ['class'=>'js-zenkaku-to-hankaku']) ?>
    </p>
    <p>
    <?= Html::submitButton('履歴を表示', ['class' => 'btn btn-success']) ?>
    </p>

    </form>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'columns' => [
        'd_item_date',
        [
            'attribute' => 'denpyo.denpyo_time',
        ],
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
        'd_item_syohin_num',
        [
            'attribute' => 'd_item_1_syohin_name_hidden',
            'filterInputOptions' => ['placeholder'=>'英数字のみ','class'=>'form-control js-zenkaku-to-hankaku'],
        ],
        [
            'attribute' => 'd_item_2_division',
            'filterInputOptions' => ['placeholder'=>'英数字のみ','class'=>'form-control'],
        ],
        [
            'attribute' => 'd_item_std_tanka',
            'format'    => 'currency',
            'contentOptions' => ['class'=>'text-right'],
            'filterInputOptions' => ['placeholder'=>'完全一致','class'=>'form-control'],
        ],
    ],

]) ?>

</div>
