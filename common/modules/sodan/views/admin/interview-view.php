<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/sodan/views/admin/interview-view.php $
 * $Id: interview-view.php 3851 2018-04-24 09:07:27Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\sodan\Interview
 */

$this->title = sprintf('%s | %s | %s', $model->itv_id, '相談会', Yii::$app->name);
$this->params['breadcrumbs'][] = ['label'=>'相談会','url'=>['interview-index']];
$this->params['breadcrumbs'][] = $model->itv_id;

Yii::$app->formatter->nullDisplay = '<span class="not-set">(セットされていません)</span>';

foreach(['presence','impression','advice','officer_use', 'summary', 'progress'] as $attr)
{
    if(! strlen(trim($model->$attr)))
        $model->$attr = null;
}

$fmt = Yii::$app->formatter;

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
                'attribute' => 'homoeopath_id',
                'format'    => 'html',
                'value'     => $model->homoeopath
                           ? Html::a($model->homoeopath->name,['interview-index','Interview[homoeopath_id]'=>$model->homoeopath_id])
                           : null,
            ],
            [
                'attribute' => 'client_id',
                'format'    => 'html',
                'value'     => ($model->client
                              ? Html::a($model->client->name,['view','id'=>$model->client_id,'target'=>'client'])
                              : null) . '&nbsp;' .
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
                'value'     => $model->status->name,
            ],
        ],
    ]) ?>

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

        <h4><?= Html::label($model->getAttributeLabel('recipe_id')) ?></h4>
        <p class="well">
            <?php if(time() < strtotime($model->itv_date)): ?>
                <span>
                    このカルテに紐付いた適用書は相談会当日(<?= $model->itv_date ?>)のみ作成可能です。
                </span>
            <?php else: ?>
            <?= $model->recipe
              ? Html::a('表示',['/recipe/admin/view','id'=>$model->recipe_id])
              : Html::a('作成',['create','id'=>$model->itv_id, 'target'=>'recipe'],['class'=>'btn btn-xs btn-primary'])
            ?>
            <?php endif ?>
        </p>

        <h4><?= $model->getAttributeLabel('officer_use') ?></h4>
        <p class="well well-sm">
            <?= Yii::$app->formatter->asNtext($model->officer_use) ?>
        </p>

    </div>

    <div class="row">
        <div class="col-md-12">

            <div class="form-group col-md-6">
                <?= Html::a('更新', ['update', 'id' => $model->itv_id, 'target' => 'interview'], ['class' => 'btn btn-primary']) ?>
            </div>

            <?= DetailView::widget([
                'options'=>['class'=>'col-md-6 table-condenced'],
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'create_date',
                        'value'     => Yii::$app->formatter->asDate($model->create_date, 'php:Y-m-d H:i ')
                                     . ($model->creator ? sprintf('(%s)', $model->creator->name01) : null),
                    ],
                    [
                        'attribute' => 'update_date',
                        'value'     => Yii::$app->formatter->asDate($model->update_date, 'php:Y-m-d H:i ')
                                     . ($model->updator ? sprintf('(%s)',$model->updator->name01) : null),
                    ],
                ],
            ]) ?>
        </div>
    </div>


</div>
