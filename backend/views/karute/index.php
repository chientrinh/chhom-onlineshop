<?php

use yii\helpers\Html;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/karute/index.php $
 * $Id: index.php 1637 2015-10-11 11:12:30Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = sprintf('%s | %s | %s', '一覧','カルテ',Yii::$app->name);

$homoeopaths = \common\models\webdb20\KaruteHomoeopath::find()->all();
$homoeopaths = \yii\helpers\ArrayHelper::map($homoeopaths, 'syoho_homeopathid', 'syoho_homeopath');
?>
<div class="karute-index">

    <h1>カルテ</h1>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel, 
        'layout'  => '{items}{pager}{summary}',
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
                'filter'    => $homoeopaths,
            ],
            [
                'attribute' => 'customerid',
                'format'    => 'html',
            ],
            
            [
                'attribute' => 'karute_syuso',
                'format'    => 'text',
                'value'     => function($data){ return \yii\helpers\StringHelper::truncate($data->karute_syuso, 48); },
            ],

        ],
    ]); ?>

</div>
