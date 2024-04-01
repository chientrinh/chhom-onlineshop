<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'HJ代理店割引率';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-grade-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'rank_id',
            'name',
            'liquor_rate',
            'goods_rate',
            'remedy_rate',
            'other_rate',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}{update}{delete}',
            ],
        ],
    ]); ?>

    <p>
<?= Html::a('追加', ['create'], ['class' => 'btn btn-success',
    'data' => [
        'confirm' => "ほんとうに追加しますか",
    ]
]) ?>
    </p>

</div>
