<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/wait-list/view.php $
 * $Id: view.php 3851 2018-04-24 09:07:27Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\sodan\WaitList
 */

$title = sprintf('%06d',$model->wait_id);
$this->params['breadcrumbs'][] = $title;
$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
rsort($labels);
$this->title = implode(' | ', $labels) . ' | '. Yii::$app->name;

?>
<div class="wait-list-view">

    <?php if(! $model->itv_id): ?>
    <p class="pull-right">
        <?= Html::a('編集', ['update', 'id' => $model->wait_id], ['class' => 'btn btn-primary']) ?>
    </p>
    <?php endif ?>

    <h1><?= Html::encode($title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'client_id',
                'format'    => 'html',
                'value'     => Html::a($model->client->name, ['client/view','id'=>$model->client_id]),
            ],
            [
                'attribute'=>'branch_id',
                'value'    => $model->branch->name,
            ],
            [
                'attribute'=>'homoeopath_id',
                'value'    => $model->homoeopath ? $model->homoeopath->homoeopathname : null,
            ],
            'note:ntext',
            [
                'attribute' => 'itv_id',
                'format'    => 'html',
                'value'     => $model->interview
                             ? Html::a($model->interview->itv_date
                                 . Yii::$app->formatter->asTime($model->interview->itv_time,'php: H:i ')
                                 . $model->interview->status->name
                                 ,['interview/view','id'=>$model->itv_id])
                             : null,
            ],
            [
                'attribute' => 'expire_date',
                'format'    => 'html',
                'value'     => Yii::$app->formatter->asDate($model->expire_date,'php:Y-m-d H:i')
                                  . ($model->isExpired() ? null : Html::a('キャンセル待ちをやめる',['cancelate','id'=>$model->wait_id],['class'=>'pull-right btn btn-xs btn-default']))
            ],
        ],
    ]) ?>

    <?php if($model->isExpired()): ?>
        <p class="alert alert-warning">
            期限切れになりました。
        </p>
    <?php endif ?>

    <?= DetailView::widget([
        'options'=>['class'=>'col-md-4 table-condenced text-right pull-right'],
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'create_date',
                'value'     => Yii::$app->formatter->asDate($model->create_date, 'php:Y-m-d H:i ')
                                  . (sprintf('(%s)', $model->creator ? $model->creator->name01 : $model->homoeopath->name)),
            ],
            [
                'attribute' => 'update_date',
                'value'     => Yii::$app->formatter->asDate($model->update_date, 'php:Y-m-d H:i ')
                                  . (sprintf('(%s)', $model->updator ? $model->updator->name01 : $model->homoeopath->name)),
            ],
        ],
    ]) ?>

</div>
