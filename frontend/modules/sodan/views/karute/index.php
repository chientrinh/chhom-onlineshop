<?php 
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/sodan/views/karute/index.php $
 * $Id: index.php 1686 2015-10-18 15:21:58Z mori $
 */

use \yii\helpers\Html;

$this->title = '一覧 | カルテ | 健康相談';

?>

<div class="consultation-default-index">

    <p class="pull-right">
        <?= Html::a('検索条件をクリア', ['index'], ['class'=>'btn btn-xs btn-default']) ?>
    </p>
    <h1>カルテ</h1>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'layout' => '{items}{pager}{summary}',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'karuteid',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->karuteid, ['view','id'=>$data->karuteid]); },
            ],
            'karute_date:ntext',
            [
                'attribute' => 'syoho_homeopathid',
                'format'    => 'html',
                'value'     => function($data){ return $data->syoho_homeopathid ? $data->homoeopath->name : null; },
                'filter'    => [$searchModel->syoho_homeopathid=>''],
            ],
            [
                'attribute' => 'customerid',
                'format'    => 'html',
                'value'     => function($data){ if($data->customer) return Html::a($data->customer->name,['index','Karute[customerid]'=>$data->customerid]); },
            ],
            [
                'attribute' => 'karute_syuso',
                'format'    => 'text',
                'value'     => function($data){ return \yii\helpers\StringHelper::truncate($data->karute_syuso, 48); },
            ],
        ],
    ]); ?>

</div>
