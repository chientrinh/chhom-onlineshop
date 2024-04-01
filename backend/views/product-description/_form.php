<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProductDescription */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-description-form">

    <?php if($model->product->descriptions)
{
    echo \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels'  => $model->product->descriptions,
            'pagination' => false,
        ]),
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'layout'       => '{items}',
        'rowOptions'=> function ($m, $key, $index, $grid)
        {
                return ['class'=>'alert alert-warning'];
        },
        'columns'      => [
            'desc_id',
            [
                'attribute' => 'title',
                'format'    => 'html',
            ],
            [
                'attribute' => 'body',
                'format'    => 'html',
            ],
        ],
    ]);
} ?>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'body')->textArea(['rows' => 5]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= !$model->isNewRecord ?  \yii\helpers\Html::a("Delete",['product-description/delete','id'=>$model->desc_id],['class'=>'btn btn-danger', 'title'=>'補足を削除します',
                    'data' =>['confirm'=>"補足「{$model->title}」を削除します。よろしいですか？"]]): '' ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?= \yii\widgets\DetailView::widget([
        'model' => $model->product,
        'attributes' => [
            'product_id',
            [
                'attribute' => 'category.name',
                'label'     => $model->product->getAttributeLabel('category'),
            ],
            'code',
            'name',
            'kana',
            [
                'attribute' => 'price',
                'format'    => 'raw',
                'value'     => sprintf("&yen;%s", number_format($model->product->price)),
            ],
            [
                'attribute' => 'summary',
                'format'    => 'html',
            ],
            [
                'attribute' => 'description',
                'format'    => 'html',
            ],
            'start_date',
            'expire_date',
        ],
    ]) ?>


</div>
