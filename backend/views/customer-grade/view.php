<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\CustomerGrade */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Customer Grades', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-grade-view">

    <p class="pull-right">
        <?= Html::a('修正', ['update', 'id' => $model->grade_id], ['class' => 'btn btn-primary']) ?>
        <!-- ?= Html::a('削除', ['delete', 'id' => $model->grade_id], [
             'class' => 'btn btn-danger',
             'data' => [
             'confirm' => 'Are you sure you want to delete this item?',
             'method' => 'post',
             ],
             ]) ? -->
    </p>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'nickname',
            'summary:html',
            'privileges:html',
        ],
    ]) ?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => \backend\models\Customer::find()->where(['grade_id'=>$model->grade_id]),
        'sort'  => [
            'attributes' => [
                'kana' => ['kana01','kana02'],
                'name' => ['kana01','kana02'],
                'pref.name' => ['pref_id'],
                'update_date',
            ],
        ],
    ]),
    'columns' => [
        'name',
        'kana',
        'pref.name',
        'update_date',
    ],
]) ?>
</div>
