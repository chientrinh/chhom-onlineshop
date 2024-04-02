<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/room/view.php $
 * $Id: view.php 3851 2018-04-24 09:07:27Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\sodan\Interview
 */

$this->params['breadcrumbs'][] = ['label' => $model->itv_id, 'url' => \yii\helpers\Url::current()];
$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
rsort($labels);
$this->title = implode(' | ', $labels) . ' | '. Yii::$app->name;

Yii::$app->formatter->nullDisplay = '<span class="not-set">(セットされていません)</span>';

$fmt = Yii::$app->formatter;

$btnCancel = null;
if((\common\models\sodan\InterviewStatus::PKEY_READY == $model->status_id) &&
   ($user = Yii::$app->user->identity) &&
    $user instanceof \backend\models\Staff)
{
    $btnCancel = Html::a('予約をキャンセルする',
                         ['cancelate','id'=>$model->itv_id],
                         ['class'=>'pull-right btn btn-xs btn-default']);
}

?>
<div class="interview-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'itv_date',
                'value'     => Yii::$app->formatter->asDate($model->itv_date,'php:Y-m-d (D) ')
                             . Yii::$app->formatter->asTime($model->itv_time,'php:H:i'),
            ],
            [
                'label'     => $model->getAttributeLabel('homoeopath_id'),
                'attribute' => 'homoeopath.name',
            ],
            [
                'attribute' => 'client_id',
                'format'    => 'html',
                'value'     => ($model->client
                              ? Html::a($model->client->name, ['client/view','id'=>$model->client_id])
                              : $fmt->asText(null)) . '&nbsp;' .
                             (($model->client && ($client = \common\models\sodan\Client::findOne($model->client_id)) && ! $client->isValid())
                                 ? Html::tag('span',implode('; ',$client->firstErrors),['class'=>'text-warning'])
                                     : ''),
            ],
            [
                'attribute' => 'product_id',
                'value'     => $model->product ? $model->product->name : null,
            ],
            [
                'attribute' => 'status_id',
                'format'    => 'raw',
                'value'     => sprintf('%s %s', $model->status->name, $btnCancel),
            ],
        ],
    ]) ?>

    <?php if($model->client && ! $model->isExpired()): ?>
    <div>
        <h4><?= Html::label($model->getAttributeLabel('complaint')) ?></h4>
        <p class="well">
            <?= $model->complaint ? $fmt->asNtext($model->complaint) : $fmt->nullDisplay ?>
        </p>

        <h4><?= Html::label($model->getAttributeLabel('presence')) ?></h4>
        <p class="well">
        <?= $model->presence ? $fmt->asNtext($model->presence) : $fmt->nullDisplay ?>
        </p>

        <h4><?= Html::label($model->getAttributeLabel('impression')) ?></h4>
        <p class="well">
        <?= $model->impression ? $fmt->asNtext($model->impression) : $fmt->nullDisplay ?>
        </p>

        <h4><?= Html::label($model->getAttributeLabel('summary')) ?></h4>
        <p class="well">
        <?= $model->summary ? $fmt->asNtext($model->summary) : $fmt->nullDisplay ?>
        </p>

        <h4><?= Html::label($model->getAttributeLabel('progress')) ?></h4>
        <p class="well">
        <?= $model->progress ? $fmt->asNtext($model->progress) : $fmt->nullDisplay ?>
        </p>

        <h4><?= Html::label($model->getAttributeLabel('advice')) ?></h4>
        <p class="well">
        <?= $model->advice ? $fmt->asNtext($model->advice) : $fmt->nullDisplay ?>
        </p>

        <h4><?= Html::label($model->getAttributeLabel('recipe')) ?></h4>
        <?php if($model->recipe): ?>
            <?= \yii\grid\GridView::widget([
                'dataProvider'=> new \yii\data\ActiveDataProvider(['query'=>$model->recipe->getItems()]),
            ]) ?>
        <?php else: ?>
            <?= $fmt->asText(null) ?>
            <?php if(time() < strtotime($model->itv_date)): ?>
                <span class="help-block">
                    このカルテに紐付いた適用書は相談会当日(<?= $model->itv_date ?>)のみ作成可能です。
                </span>
            <?php else: ?>
                <?= Html::a('作成',['create','id'=>$model->itv_id, 'target'=>'recipe'],['class'=>'btn btn-xs btn-primary']) ?>
            <?php endif ?>
        <?php endif ?>

    <?php endif ?>

        <h4><?= $model->getAttributeLabel('officer_use') ?></h4>
        <p class="well">
            <?= Yii::$app->formatter->asNtext($model->officer_use) ?>
        </p>

    </div>

    <div class="row">
        <div class="col-md-12">

            <div class="form-group col-md-6">
                <?= Html::a('編集', ['update', 'id' => $model->itv_id, 'target' => 'interview'], ['class' => 'btn btn-primary']) ?>
            </div>

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
    </div>


</div>
