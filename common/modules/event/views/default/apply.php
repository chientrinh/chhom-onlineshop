<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var $this yii\web\View
 * @var $model  common\models\EventVenue
 */

$this->params['body_id'] = 'Product';
$this->params['breadcrumbs'][] = ['label' => 'イベント', 'url' => ['/category/viewbyname','name'=>'イベント']];

?>

<div class="product">

<div class="col-md-4 product-photo">

    <?= $this->render('_image', ['model'=>$event]) ?>

</div>

<div class="col-md-8 product-detail">

    <?= $this->render('_detail', ['model'=>$event]) ?>

    <h4><?= $venue->name ?></h4>
    <h4><?= $venue->event_date ?></h4>
    <h4><?= $venue->start_time ?></h4>
    <h4><?= $venue->end_time ?></h4>

    <?php $form = \yii\bootstrap\ActiveForm::begin() ?>

    <?= $form->field($model, 'adult') ?>

    <?php if($venue->allow_child): ?>
    <?= $form->field($model, 'child') ?>
    <?php endif ?>

    <?= Html::submitButton('予約',['class'=>'btn btn-warning']) ?>

    <p class="help-block">
        予約ボタンをクリックすると<?= Html::a('カートの中',['/cart/default/index']) ?>に入ります。
        <?= Html::a('カートの中',['/cart/default/index']) ?>にて注文を確定すると予約手続きが完了します。
    </p>

    <?php $form->end() ?>

</div>
</div>
