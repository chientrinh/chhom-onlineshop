<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/subcategory/_form.php $
 * $Id: _form.php 3268 2017-04-21 01:45:07Z kawai $
 *
 * @var $this yii\web\View
 * @var $model common\models\Subcategory
 * @var $form yii\widgets\ActiveForm
 */

$companies = \common\models\Company::find()->all();
$companies = \yii\helpers\ArrayHelper::map($companies, 'company_id', 'name');

$query = \common\models\Subcategory::find()
                                   ->andWhere(['company_id'=>$model->company_id])
                                   ->andFilterWhere(['not',['subcategory_id' => $model->subcategory_id]]);
if(0 < $model->subcategory_id)
    $query->andWhere(['or',['not',['parent_id' => $model->subcategory_id]],
                         ['parent_id' => null]]);

$parents = \yii\helpers\ArrayHelper::map($query->all(), 'subcategory_id', 'fullname');
$parents[0] = '（なし）';
if(null === $model->parent_id)
    $model->parent_id = 0;

$restricts = \common\models\ProductRestriction::find()->all();
$restricts = \yii\helpers\ArrayHelper::map($restricts, 'restrict_id', 'name');

$disabled = ['disabled'=>'disabled'];

?>

<div class="subcategory-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'company_id')->dropDownList($companies, $disabled) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'parent_id')->dropDownList($parents) ?>

    <?= $form->field($model, 'restrict_id')->dropDownList($restricts) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '作成' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

    <?php if(! $model->isNewRecord): ?>
        <?= Html::a('削除',['delete','id'=>$model->subcategory_id],[
        'class'=>'btn btn-danger pull-right ' . ($model->getProducts()->exists() ? 'disabled' : null),
        'data' => [
            'confirm' => '本当に削除していいですか',
        ],
        ]) ?>
        <?php if($model->products): ?>
        <p class='hint-block text-right'>このサブカテゴリーに属する商品があるため削除できません</p>
        <?php endif ?>
    <?php endif ?>

    <?php $form->end(); ?>

    </div>

</div>
