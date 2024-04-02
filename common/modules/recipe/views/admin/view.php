<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/views/admin/view.php $
 * $Id: view.php 4055 2018-11-07 06:59:29Z kawai $
 *
 * @var $this yii\web\View
 * @var $model common\models\Recipe
 */

$this->params['body_id'] = 'View';
$this->params['breadcrumbs'][] = ['label' => sprintf('%06d',$model->recipe_id)];
$labels = \yii\helpers\ArrayHelper::getColumn($this->params['breadcrumbs'],'label');
krsort($labels);
$this->title = implode(' | ', $labels) . ' | ' . Yii::$app->name;

$expire_date = Yii::$app->formatter->asDate($model->expire_date, 'php:Y-m-d H:i');
?>
<div class="recipe-view">

    <h1><?= $model->getAttributeLabel('recipe_id') ?> : <?= $model->recipe_id ?></h1>

    <?php if(! $model->isExpired() && ! $model->isSold()): ?>
    <p class="pull-left">
        <?= Html::a('レジに追加', ['/casher/default/apply', 'target'=>'recipe', 'id' => $model->recipe_id, 'preview'=>true], ['class' => 'btn btn-success']) ?>
        <?php if ($model->publish_flg): ?>
            <?= Html::a('非公開にする', ['close', 'id' => $model->recipe_id], [
                'class' => 'btn btn-default',
                'data' => [
                    'confirm' => 'この適用書を顧客非公開にしますか',
                    'method'  => 'post',
               ],
           ]) ?>
        <?php else: ?>
            <?= Html::a('公開する', ['publish', 'id' => $model->recipe_id], [
                 'class' => 'btn btn-default',
                 'data' => [
                     'confirm' => 'この適用書を顧客に公開してよろしいですか。',
                     'method'  => 'post',
                ],
            ]) ?>
        <?php endif; ?>
        <?php if ($model->itv_id): ?>
            <?= Html::a('カルテに戻る', ['/sodan/interview/view', 'id' => $model->itv_id, 'target' => 'interview'], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
    </p>
    <?php endif; ?>

    <p class="pull-right">
        <?= Html::a('プレビュー', ['view', 'id' => $model->recipe_id, 'preview'=>true], ['class' => 'btn btn-default']) ?>
        <?= Html::a('印刷', ['print','id'=>$model->recipe_id, 'format'=>'pdf'],['class'=>'btn btn-xl btn-warning','target'=>'_blank'])?>
        <?php $disabled = ($model->status === common\models\Recipe::STATUS_SOLD) ? 'disabled' : null; ?>
        <?= Html::a('編集', ['updateedit', 'id' => $model->recipe_id], ['class' => 'btn btn-primary ' . $disabled, 'title'=>'この適用書を編集します。']) ?>
        <?= Html::a('再作成', ['update', 'id' => $model->recipe_id], ['class' => 'btn btn-info','title'=>'この適用書を元にして新たな適用書を作成します。']) ?>
        <?= Html::a('コピー', ['updatekeepsts', 'id' => $model->recipe_id], ['class' => 'btn btn-success','title'=>'この適用書を元にして新たな適用書を作成します。（この適用書のステータスは変更されません）']) ?>
        <?php if(! $model->isExpired() && ! $model->isSold()): ?>
        <?= Html::a('無効にする', ['delete', 'id' => $model->recipe_id], [
             'class' => 'btn btn-danger',
             'data' => [
                 'confirm' => '本当にこの適用書を無効にしてもいいですか。',
                 'method'  => 'post',
            ],
        ]) ?>
        <?php endif ?>
    </p>

<?php if($model->isSold()): ?>
<p class="alert alert-success">レメディーは購入済みです</p>
<?php elseif($model->isExpired()): ?>
<p class="alert alert-danger">この適用書は無効です</p>
<?php endif ?>

<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'attribute'=>'homoeopath_id',
            'format'   => 'html',
            'value'    => $model->homoeopath
            ? Html::a($model->homoeopath->homoeopathname,['/customer/view','id'=>$model->homoeopath->customer_id])
            : null,
        ],
        [
            'attribute'=> 'client_id',
            'format'   => 'html',
            'value'    => $model->client
            ? Html::a($model->client->name,['/customer/view','id'=>$model->client->customer_id])
            : ($model->manual_client_name ?: null),
        ],
        [
            'attribute'=> 'staff_id',
            'format'   => 'html',
            'value'    => $model->staff
            ? Html::a($model->staff->name,['/staff/view','id'=>$model->staff->staff_id])
            : null,
        ],
        'center',
        'tel',
        'create_date:date',
        'update_date:date',
        [
            'attribute' => 'expire_date',
            'format' => 'html',
            'value'  => $model->isExpired() ? Html::tag('span', $expire_date, ['class'=>'text-danger']) : $expire_date,
        ],
        [
            'attribute'=>'status',
            'format' => 'html',
            'value'  => $model->isExpired()
                ? Html::tag('span', $model->statusName, ['class'=>'text-danger'])
                . Html::a('再購入を許可',['reset','id'=>$model->recipe_id],['class'=>'btn btn-xs btn-default pull-right'])
                : $model->statusName ,
        ],
        'note',
    ],
]) ?>

<?php if('debug' === Yii::$app->request->get('target')): ?>

    <h2>DEBUG</h2>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query'      => \common\models\RecipeItem::find()->where(['recipe_id'=>$model->recipe_id]),
            'pagination' => false,
        ]),
    ]) ?>

<?php else: ?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => \common\models\RecipeItem::find()->where(['recipe_id'=>$model->recipe_id,
                                                             'parent'   => null]),
        'pagination' => false,
        'sort' => [
            'attributes' => [
                'name',
                'remedy' => [
                    'default' => SORT_DESC,
                ],
                'remedy' => [
                    'asc' => ['remedy.abbr' => SORT_ASC  ],
                    'desc'=> ['remedy.abbr' => SORT_DESC ],
                    'default' => SORT_ASC,
                ],
                'potency' => [
                    'asc' => ['potency.weight' => SORT_ASC  ],
                    'desc'=> ['potency.weight' => SORT_DESC ],
                    'default' => SORT_ASC,
                ],
                'vial' => [
                    'asc' => ['vial_id' => SORT_ASC  ],
                    'desc'=> ['vial_id' => SORT_DESC ],
                    'default' => SORT_ASC,
                ],
                'seq',
                'quantity',
            ],
            'defaultOrder' => ['seq' => SORT_ASC ],
        ],
    ]),
    'columns' => [
        [
            'class'=> '\yii\grid\SerialColumn',
        ],
        [
            'attribute' => 'name',
            'format'    => 'html',
            'value'     => function($data)
            {
                return nl2br($data->fullname);
            },
        ],
        [
            'attribute'=>'potency',
            'value'    => function($data)
            {
                if($data->getChildren()->exists())
                    return null;

                if($data->potency)
                    return $data->potency->name;
            }
        ],
        [
            'attribute'=>'vial',
            'value'    => function($data)
            {
                if($data->vial)
                return $data->vial->name;
            }
        ],
        [
            'attribute'=> 'instruction',
            'value'    => function($data)
            {
                if($data->instruction)
                return $data->instruction->name;
            }
        ],
        'memo',
        'quantity',
    ],
])?>

<p class="pull-right">
<?= HTml::a('debug',['view','id'=>$model->recipe_id,'target'=>'debug'],['class'=>'btn btn-xs btn-default']) ?>
</p>

<?php endif ?>

</div>
