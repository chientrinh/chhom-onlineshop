<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product/index_csv.php $
 * $Id: index_csv.php 4187 2019-10-02 05:21:00Z mori $
 *
 * @var $this yii\web\View
 * @var $searchModel common\models\ProductSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = "全商品一括CSV出力";
$this->params['breadcrumbs'][] = ['label'=>$this->title];

$categories = \yii\helpers\ArrayHelper::map(\common\models\Category::find()->all(), 'category_id', 'name');
$companies  = \yii\helpers\ArrayHelper::map(\common\models\Company::find()->all(),  'company_id', 'name');
array_unshift($companies,'');

$taxes  = \yii\helpers\ArrayHelper::map(\common\models\Tax::find()->all(),  'tax_id', 'name');

if($searchModel->company)
    $categories = \yii\helpers\ArrayHelper::map(\common\models\Category::find()->where(['seller_id'=>$searchModel->company])->all(), 'category_id', 'name');

?>

<div class="product-index">

<div class="row">
<div class="col-md-6">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
</div>
<?php // テナント時には非表示とする
    if(!Yii::$app->user->identity->hasRole(["tenant"])){ ?>
<div class="col-md-6">
    <p class="dropdown-menu-right">
    <?php $form = ActiveForm::begin([
        'action'=> \yii\helpers\Url::current(),
        'method'=>'get']);
    ?>
    <?= $form->field($searchModel, 'company')->label(false)->dropDownList($companies,[
        'onChange' => 'this.form.submit()',
        'name'     => 'company',
    ]) ?>
    <?php ActiveForm::end(); ?>
    </p>
</div>
<?php } ?>
</div>

product_id,category_id,category.name,vendor.key,seller.key,code,pickcode,name,kana,price,tax_id,start_date,expire_date<br>
<?php foreach($dataProvider->models as $model): ?>
<?= implode(',', [
    $model->product_id,
    $model->category_id,
    ArrayHelper::getValue($model,'category.name'),
    ArrayHelper::getValue($model,'category.vendor.key'),
    ArrayHelper::getValue($model,'category.seller.key'),
    $model->code,
    $model->pickcode,
    $model->name,
    $model->kana,
    $model->price,
    ArrayHelper::getValue($taxes,$model->tax_id),
    Yii::$app->formatter->asDate($model->start_date,'php:Y-m-d'),
    Yii::$app->formatter->asDate($model->expire_date,'php:Y-m-d'),
]) ?><br>
<?php endforeach ?>

</div>
