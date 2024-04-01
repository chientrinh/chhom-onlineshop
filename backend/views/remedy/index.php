<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy/index.php $
 * $Id: index.php 3196 2017-02-26 05:11:03Z naito $
 *
 * @var $this     yii\web\View
 * @var $model    common\models\SearchRemedy
 * @var $provider yii\data\ActiveDataProvider
 * @var $format   string
 */

if('csv' == $format)
{
    $provider->pagination = false;

    echo yii\widgets\ListView::widget([
      'dataProvider' => $provider,
      'layout'       => '{items}',
      'itemView'     => '_csv',
    ]);

    return;
}

$this->title = "レメディー";
$this->params['breadcrumbs'][] = ['label'=>$this->title, 'url'=>['index']];
?>
<div class="remedy-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $provider,
        'filterModel' => $model,
        'layout' => ''
          . Html::a('全件表示', ['index','pagination'=>'false'])
          . ' / '
          . Html::a('CSV', ['index','format'=>'csv'])
          . '{summary}{pager}{items}{pager}', 
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'abbr',
                'format'    => 'html',
                'value'     => function($data){ return Html::a($data->abbr, ['viewbyname','name'=>$data->abbr]); },
            ],
            'latin',
            'ja',
            'concept',
            [
                'attribute' => 'on_sale',
                'filter'    => [1 => "OK", 0 => "NG"],
                'value'     => function($data){ return $data->on_sale ? "OK" : "NG"; },
            ],
            [
                'attribute' => 'restrict_id',
                'filter'    => \yii\helpers\ArrayHelper::map(\common\models\ProductRestriction::find()->all(), 'restrict_id', 'name'),
                'value'     => function($data){ return $data->restriction->name; },
            ],

            [
                'header' => "ポーテンシー" . '&nbsp;'. Html::a("もっと詳しく", \yii\helpers\Url::toRoute('/remedy-stock'),['class'=>'btn btn-xs btn-default','title'=>"レメディー品揃えヘ"]),
                'value' => function($data){
                    return implode(', ',\yii\helpers\ArrayHelper::getColumn($data->potencies,'potency.name'));
                },
            ]

        ],
    ]); ?>

    <p>
        <?= Html::a("レメディーを追加", ['create'], ['class' => 'btn btn-success']) ?>
    </p>
</div>
