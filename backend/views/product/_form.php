<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use \common\models\Company;
use \common\models\SalesCategory;
use \common\models\SalesCategory1;
use \common\models\SalesCategory2;
use \common\models\SalesCategory3;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product/_form.php $
 * $Id: _form.php 2562 2016-06-04 05:37:25Z mori $
 *
 * @var $this yii\web\View
 * @var $form yii\widgets\ActiveForm
 * @var $model common\models\Product
 */

$salesInfo = SalesCategory::find()->where(['sku_id' => $model->sku_id])->one();
if($salesInfo) {
    $sales1Info = $salesInfo->sales1;
    $sales2Info = $salesInfo->sales2;
    $sales3Info = $salesInfo->sales3;
    if($sales1Info && $sales2Info && $sales3Info) {
        $model->bunrui_code1 = $sales1Info->bunrui_id -1;
        $model->bunrui_code2 = $sales2Info->bunrui_id -1;
        $model->bunrui_code3 = $sales3Info->bunrui_id -1;
        $company_key = $salesInfo->vender_key;
	if($company_key == 'TR')
            $company_key = 'trose';

        $model->vender_key = Company::find()->where(['key' => $company_key])->one()->company_id - 1;
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


$categories = \common\models\Category::find()->all();
\yii\helpers\ArrayHelper::multisort($categories, ['seller_id','category_id']);
$categories = \yii\helpers\ArrayHelper::map($categories, 'category_id', function($elem){ return sprintf('%s:%s', strtoupper($elem->seller->key), $elem->name); } );

$restrictions = \common\models\ProductRestriction::find()->all();
$restrictions = \yii\helpers\ArrayHelper::map($restrictions, 'restrict_id', 'name');

$tax =  \yii\helpers\ArrayHelper::map(\common\models\Tax::find()->all(), 'tax_id', 'name');

$formats    = \yii\helpers\ArrayHelper::map(\common\models\BookFormat::find()->all(), 'format_id', 'name');

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
if(in_array('tenant', array_keys($roles)))
    $swapCategory = false; // if user is tenant, do not allow update category_id
else
    $swapCategory = true;
?>

<div class="product-form">

    <?php $form = ActiveForm::begin([
        'method'=>'post',
    ]); ?>

    <div class="form-group">

<?php if($model->isNewRecord): ?>

  <?= Html::submitButton("保存", ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

<?php else: ?>

  <?= Html::submitButton("更新", ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

<?php endif ?>

    </div>

    <?= $form->field($model, 'category_id')->dropDownList($categories,['disabled'=>!$swapCategory]) ?>

    <?= $form->field($model, 'restrict_id')->dropDownList($restrictions) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => 255, 'class'=>'form-control js-zenkaku-to-hankaku']) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'kana')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'price')->textInput(['class'=>'form-control js-zenkaku-to-hankaku']) ?>

    <?= $form->field($model, 'tax_id')->dropDownList($tax) ?>

    <?= $form->field($model, 'in_stock')->dropDownList([1=>'OK',0=>'NG']) ?>

    <?= $form->field($model, 'liquor_flg')->dropDownList([0 => 'いいえ', 1 => 'はい']) ?>

    <?= $form->field($model, 'upper_limit')->textInput() ?>

    <?php if (!$model->restrict_id): ?>
        <?= $form->field($model, 'recommend_flg')->dropDownList([0 => '表示しない', 1 => '表示する']) ?>

        <?= $form->field($model, 'recommend_seq')->textInput(['maxlength' => 8]) ?>
    <?php endif;?>

    <?= $form->field($model, 'keywords')->textArea(['rows'=> 3]) ?>
    <?= $form->field($model, 'summary')->textArea(['rows'=> 3, 'maxlength' => 255]) ?>
    <?= $form->field($model, 'description')->textArea(['rows' => 5]) ?>

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
    <?= $form->field($model, 'vender_key')->dropDownList($companies, ['class'=>'form-control js-input-label']) ?>
    <?= $form->field($model, 'bunrui_code1')->dropDownList($salesArray1, ['class'=>'form-control js-input-label']) ?>
    <?= $form->field($model, 'bunrui_code2')->dropDownList($salesArray2, ['class'=>'form-control js-input-label']) ?>
    <?= $form->field($model, 'bunrui_code3')->dropDownList($salesArray3, ['class'=>'form-control js-input-label']) ?>


    <div class="form-group">

<?php if($model->isNewRecord): ?>

  <?= Html::submitButton("保存", ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

<?php else: ?>


<?php if($model->isBook()): ?>
<?php if($model->bookinfo): ?>
<h2>書誌</h2>
    <?= \yii\widgets\DetailView::widget([
        'model' => $model->bookinfo,
        'attributes' => [
            'author',
            'translator',
            'page',
            'pub_date',
            'publisher',
            'format.name',
            'isbn',
        ],
    ]) ?>
<?= \yii\helpers\Html::a("書誌を編集",['book/update','id'=>$model->product_id],['class'=>'btn btn-xs btn-primary']) ?>
<?php else: ?><!--no bookinfo yet-->
<?= \yii\helpers\Html::a("書誌を追加",['book/create','id'=>$model->product_id],['class'=>'btn btn-xs btn-success']) ?>
<?php endif ?><!--end of $model->bookinfo-->
<?php endif ?><!--end of $model->isBook()-->

<h2>補足</h2>

<?= \yii\helpers\Html::a("補足を追加",['product-description/create','id'=>$model->product_id],['class'=>'btn btn-xs btn-success']) ?>

<?php if($model->descriptions): ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels'  => $model->descriptions,
            'pagination' => false,
        ]),
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'layout'       => '{items}',
        'columns'      => [
            'title',
            [
                'attribute' => 'body',
                'format'    => 'html',
            ],
            [
                'label' => 'edit',
                'format'=> 'html',
                'value' => function($data){
                    return \yii\helpers\Html::a("編集", ['product-description/update','id'=>$data->desc_id],['class'=>'btn btn-xs btn-primary']);
                },
            ],
            [
                'label' => 'delete',
                'format'=> 'raw',
                'value' => function($data){
                    return \yii\helpers\Html::a("削除", ['product-description/delete','id'=>$data->desc_id],['class'=>'btn btn-xs btn-danger', 'title'=>'補足を削除します', 'data' =>['confirm'=>"補足「{$data->title}」を削除します。よろしいですか？"]]);
                },
            ],
        ],
    ]) ?>
<?php endif ?>


<h2>画像</h2>
<div class="row">

    <?php foreach($model->images as $image): ?>
        <div class="thumbnail col-xs-6 col-md-3">
                <?= Html::a(' ',['product-image/delete','id'=>$image->img_id],[
                    'class'=>'btn btn-xs btn-danger glyphicon glyphicon-remove pull-right',
                    'title'=>'画像を削除します',
                    'data' =>['confirm'=>"画像を削除していいですか"],
                ]) ?>
                <?= Html::img($image->url, ['alt'=> $image->basename, 'style'=>'max-width:100px;max-height:100px']) ?>
                <?= Html::a(' ',['product-image/update','id'=>$image->img_id,'weight'=>$image->weight+1],[
                    'class'=>'btn btn-xs btn-info glyphicon glyphicon-chevron-left pull-left',
                    'title'=>'画像を上位に移動します',
                ]) ?>
                <?= Html::a(' ',['product-image/update','id'=>$image->img_id,'weight'=>$image->weight-1],[
                    'class'=>'btn btn-xs btn-info glyphicon glyphicon-chevron-right pull-right',
                    'title'=>'画像を下位に移動します',
                ]) ?>
            <p class="text-center text-muted"><?= $image->weight ?></p>
        </div>
    <?php endforeach ?>

    <div class="col-xs-3 col-md-1">
        <span class="thumbnail pull-left">
            <?= Html::a(' ',['product-image/add','id'=>$model->product_id],['class'=>'btn btn-xs btn-success glyphicon glyphicon-plus pull-center','title'=>'画像を追加します']) ?>
        </span>
    </div>

</div>

  <?= Html::submitButton("更新", ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

  <p class="pull-right">
  <?= $model->isExpired()
      ? Html::a("販売を再開", ['activate', 'id' => $model->product_id], [
               'class' => 'btn btn-danger',
               'data' => [
                   'confirm' => sprintf("%sを販売中に戻しますか", $model->name),
               ]
        ])
      : Html::a("販売を終了", ['expire', 'id' => $model->product_id], [
               'class' => 'btn btn-danger',
               'data' => [
                   'confirm' => sprintf("%sを販売終了にしていいですか", $model->name),
               ]
        ])
  ?>
  </p>

<?php endif ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
