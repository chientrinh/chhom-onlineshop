<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Customer Grades';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-grade-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'grade_id',
            'nickname',
            'name',
            'summary:html',
            'privileges:html',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}{update}',
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
