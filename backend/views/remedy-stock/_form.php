<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use \common\models\Company;
use \common\models\SalesCategory;
use \common\models\SalesCategory1;
use \common\models\SalesCategory2;
use \common\models\SalesCategory3;


/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy-stock/_form.php $
 * $Id: _form.php 2497 2016-05-07 01:44:41Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\RemedyStock * @var $form yii\widgets\ActiveForm
 */
$potencies = \yii\helpers\ArrayHelper::map(\common\models\RemedyPotency::find()->all(), 'potency_id', 'name');
$vials     = \yii\helpers\ArrayHelper::map(\common\models\RemedyVial::find()->all(), 'vial_id', 'name');

//$pranges   = \common\models\PrangeItem::find()->where(['vial_id'=>$model->vial_id])->all();
$pranges   = \yii\helpers\ArrayHelper::map(\common\models\RemedyPriceRangeItem::find()->where(['vial_id'=>$model->vial_id])->all(), 'prange_id', function($data){ return sprintf('%s : %s', Yii::$app->formatter->asCurrency($data->price), $data->prange->name); });

$restrictions = \common\models\ProductRestriction::find()->all();
$restrictions = \yii\helpers\ArrayHelper::map($restrictions, 'restrict_id', 'name');

$salesInfo = SalesCategory::find()->where(['sku_id' => $model->code])->one();
if($salesInfo) {
    $sales1Info = $salesInfo->sales1;
    $sales2Info = $salesInfo->sales2;
    $sales3Info = $salesInfo->sales3;
    if($sales1Info && $sales2Info && $sales3Info) {
        $model->bunrui_code1 = $sales1Info->bunrui_id -1;
        $model->bunrui_code2 = $sales2Info->bunrui_id -1;
        $model->bunrui_code3 = $sales3Info->bunrui_id -1;
        $model->vender_key = Company::find()->where(['key' => $salesInfo->vender_key])->one()->company_id - 1;
    } else {
        $model->bunrui_code1 = 0;
        $model->bunrui_code2 = 0;
        $model->bunrui_code3 = 0;
        $model->vender_key = 0;
    }
} else {
    $model->bunrui_code1 = 0;
    $model->bunrui_code2 = 0;
    $model->bunrui_code3 = 0;
    $model->vender_key = 0;
}

$sales1 = SalesCategory1::find()->asArray()->all();
$sales2 = SalesCategory2::find()->asArray()->all();
$sales3 = SalesCategory3::find()->asArray()->all();
$salesArray1 = ArrayHelper::getColumn($sales1, function ($element) {
    return $element['bunrui_code1']." ".$element['name'];
});
$salesArray2 = ArrayHelper::getColumn($sales2, function ($element) {
    return $element['bunrui_code2']." ".$element['name'];
});
$salesArray3 = ArrayHelper::getColumn($sales3, function ($element) {
    return $element['bunrui_code3']." ".$element['name'];
});

$companies = ArrayHelper::getColumn(Company::find()->asArray()->all(), function ($element) {
    return $element['key']." ".$element['name'];
});

?>

<div class="remedy-stock-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model->remedy, 'name')->textInput(['disabled'=>'disabled']) ?>

    <?= $form->field($model, 'potency_id')->dropDownList($potencies,['disabled'=>'disabled']) ?>

     <?= $form->field($model, 'vial_id')->dropDownList($vials, ['disabled'=>'disabled']) ?>

     <?= $form->field($model, 'restrict_id')->dropDownList($restrictions) ?>

     <?= $form->field($model, 'prange_id')->dropDownList($pranges)?>

    <?= $form->field($model, 'in_stock')->dropDownList([0=>'いいえ',1=>'はい']) ?>

    <?= $form->field($model, 'vender_key')->dropDownList($companies, ['class'=>'form-control js-input-label']) ?>
    <?= $form->field($model, 'bunrui_code1')->dropDownList($salesArray1, ['class'=>'form-control js-input-label']) ?>
    <?= $form->field($model, 'bunrui_code2')->dropDownList($salesArray2, ['class'=>'form-control js-input-label']) ?>
    <?= $form->field($model, 'bunrui_code3')->dropDownList($salesArray3, ['class'=>'form-control js-input-label']) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '追加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php if(! $model->isNewRecord): ?>
        <p class="pull-right">
        <?= Html::a('削除', ['delete','remedy_id'=>$model->remedy_id,'potency_id'=>$model->potency_id,'vial_id'=>$model->vial_id], ['class' => 'btn btn-danger', 'title' => 'この容器での既製品販売を中止します']) ?>
        </p>
        <?php endif ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
