<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Information */

$title = "お知らせ";
$this->title = sprintf("%s | %s | %s", $model->info_id, $title, Yii::$app->name);
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['index']];
$this->params['breadcrumbs'][] = sprintf('id: %s',$model->info_id);
?>

<div class="information-view">

<?php if($model->isExpired()): ?>
<p class="alert alert-danger">このお知らせは失効しました</p>
<?php endif ?>

<p> <?= Html::a($model->company->name, ['/company/view', 'id'=>$model->company_id]) ?> のお知らせ</p>
<div class='well'>

<?php
echo \yii\jui\Tabs::widget([
    'items' => [
        [
            'label'   => 'html',
            'content' => $model->renderContent(),
        ],
        [
            'label'   => 'text/plain',
            'content' => Html::encode($model->renderContent()),
        ],
    ],
    'options' => ['tag' => 'div'],
    'itemOptions' => ['tag' => 'div'],
    'headerOptions' => ['class' => 'text-left'],
    'clientOptions' => ['collapsible' => false],
]);
?>

</div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'url',
                'format'    => 'url',
            ],
            'company.name',
            'pub_date:date',
            'expire_date',
            'update_date',
            [
                'attribute' => 'created_by',
                'format'    => 'html',
                'value'     => Html::a($model->creator->name, ['/staff/view', 'id'=>$model->created_by]),
            ],
            [
                'attribute' => 'updated_by',
                'format'    => 'html',
                'value'     => Html::a($model->updator->name, ['/staff/view', 'id'=>$model->updated_by]),
            ],
        ],
    ]) ?>

    <p>
        <?= Html::a("修正", ['update', 'id' => $model->info_id], ['class' => 'btn btn-primary']) ?>

        <?php if($model->isExpired()): ?>
        <?= Html::a("復活", ['activate', 'id' => $model->info_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '本当にこの項目を復活させますか?',
                'method' => 'post',
            ],
        ]) ?>
       <?php else: ?>
        <?= Html::a("失効", ['expire', 'id' => $model->info_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '本当にこの項目を失効にしていいですか?',
                'method' => 'post',
            ],
        ]) ?>
       <?php endif ?>

    </p>

</div>
