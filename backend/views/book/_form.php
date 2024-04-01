<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/book/_form.php $
 * $Id: _form.php 2056 2016-02-10 07:46:45Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Book
 * @var $form yii\widgets\ActiveForm
 */

$formats    = \yii\helpers\ArrayHelper::map(\common\models\BookFormat::find()->all(), 'format_id', 'name');

?>
<div class="book-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'product_id')->textInput(['disabled'=>'disabled']) ?>

    <?= $form->field($model, 'author')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'translator')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'page')->textInput(['class'=>'js-zenkaku-to-hankaku form-control']) ?>

    <?= $form->field($model, 'isbn')->textInput(['class'=>'js-zenkaku-to-hankaku form-control']) ?>

    <?= $form->field($model, 'pub_date')->textInput([
                'filter' => \yii\jui\DatePicker::widget([
                    'model'      => $model,
                    'attribute'  => 'pub_date',
                    'language'   => 'ja',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options'    => ['class'=>'form-control col-md-12'],
                    'clientOptions' => [
                        'country'     => 'JP',
                        'yearRange'   => 'c-5:c+1',
                        'changeYear'  => true,
                        'changeMonth' => true,
                    ],
                ])
    ]) ?>

    <?= $form->field($model, 'publisher')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'format_id')->dropDownList($formats) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
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
