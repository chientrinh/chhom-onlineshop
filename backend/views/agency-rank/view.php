<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
$this->title = $rank->name;
$this->params['breadcrumbs'][] = ['label' => 'HJ代理店割引率', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-grade-view">

    <p class="pull-right">
        <?= Html::a('修正', ['update', 'id' => $rank->rank_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $rank,
        'attributes' => [
            'name',
            'liquor_rate',
            'goods_rate',
            'remedy_rate',
            'other_rate',
            'create_date',
            'update_date'
        ],
    ]) ?>

    <div class="col-md-9">
        <div class="row">
            <?= $this->render($viewFile, ['rank'=>$rank,'dataProvider'=>$dataProvider]); ?>
        </div>
    </div>

</div>
