<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/recipe/views/review/view.php $
 * $Id: view.php 2820 2016-08-07 06:48:39Z mori $
 */

use \yii\helpers\Html;

$title = Yii::$app->controller->crumbs[$this->context->action->id]['label'];
$this->title = sprintf('%s | %s | %s', $title, '適用書', Yii::$app->name);

$this->params['body_id']        = 'Mypage';

?>

<div class="cart-view">

  <div class="col-md-9">
    <h2><span><?= $title ?> : <?= sprintf('%06d', abs($model->recipe_id)) ?></h2>

    <p class="pull-right">
    <?= Html::a('購入する', [
        '/cart/recipe/add','id'=>$model->recipe_id,'pw'=>$model->pw,
    ],[
        'class'=>'btn btn-xl btn-danger'
    ])?>
    </p>

<?= \yii\grid\GridView::widget([
    'dataProvider' => new \yii\data\ArrayDataProvider([
        'allModels'  => $model->parentItems,
        'pagination' => false,
        'sort'       => false,
    ]),
    'layout'  => '{items}',
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
        'quantity',
    ],
])?>

<?= \yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'label'     => '状態',
            'attribute' => 'statusName',
            'format'    => 'html',
            'value'     => Html::tag('strong',$model->statusName),
        ],
        [
            'attribute' => 'create_date',
            'format'    => ['date', 'php:Y-m-d D H:i'],
        ],
        [
            'attribute' => 'expire_date',
            'format'    => ['date', 'php:Y-m-d D H:i'],
        ],
    ],
]) ?>


</div>

</div>
