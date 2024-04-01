<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product/_search.php $
 * $Id: _search.php 3260 2017-04-19 08:56:53Z kawai $
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\ProductSearch */
/* @var $form yii\widgets\ActiveForm */


$query = \common\models\Subcategory::find()->where(['company_id'=>array_keys($companies)])
                                           ->andWhere(['parent_id' => null])
                                           ->orderBy('weight DESC, subcategory_id ASC');
$subcategories = [];
foreach($query->each() as $sub)
{
    $key = $sub->subcategory_id;
    $subcategories[$key] = $sub->fullname;

    foreach(mapChildren($sub) as $k => $v)
    {
        $subcategories[$k] = $v;
    }
}

function mapChildren($sub)
{
    $ret = [];

    foreach($sub->children as $child)
    {
        $k = $child->subcategory_id;
        $ret[$k] = $child->fullname;

        if($child->getChildren()->exists())
        foreach(mapChildren($child) as $k => $v)
        {
            $ret[$k] = $v;
        }
    }
    return $ret;
}
?>

<div class="well">

    <?php $form = \yii\bootstrap\ActiveForm::begin([
        'action' => ['search','company'=>Yii::$app->request->get('company')],
        'method' => 'get',
        'layout' => 'horizontal',
    ]); ?>

    <div class="col-md-6">

    <?php if(!Yii::$app->user->identity->hasRole(["tenant"])) {
        $form->field($model, 'company')->dropDownList($companies);
    } ?>

    <?= $form->field($model, 'category_id')->dropDownList($categories) ?>

    <?= $form->field($model, 'code') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'kana') ?>

    <?= $form->field($model, 'price') ?>

    <?= $form->field($model, 'start_date')->textInput([
                'filter' => \yii\jui\DatePicker::widget([
                    'model' => $model,
                    'attribute'=>'start_date',
                    'language' => 'ja',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => ['class'=>'form-control col-md-12'],
                    'clientOptions' => [
                        'country'     => 'JP',
                        'yearRange'   => 'c-1:c+1',
                        'changeYear'  => true,
                        'changeMonth' => true,
                    ],
                ])]
    ) ?>

    <?= $form->field($model, 'expire_date')->textInput([
                'filter' => \yii\jui\DatePicker::widget([
                    'model' => $model,
                    'attribute'=>'expire_date',
                    'language' => 'ja',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => ['class'=>'form-control col-md-12'],
                    'clientOptions' => [
                        'country'     => 'JP',
                        'yearRange'   => 'c-1:c+1',
                        'changeYear'  => true,
                        'changeMonth' => true,
                    ],
                ])]
    ) ?>

    </div>

    <div class="col-md-6">
        <?= $form->field($model,'subcategory')->listBox($subcategories,['multiple'=>'multiple','size'=>20,
        ]) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('検索', ['class' => 'btn btn-info']) ?>
    </div>

    </div>

    <?php $form->end(); ?>

</div>
