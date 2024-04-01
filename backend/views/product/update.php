<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use \common\models\Subcategory;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/product/update.php $
 * $Id: update.php 3539 2017-08-10 03:02:53Z kawai $
 *
 * @var $this yii\web\View
 * @var $model common\models\Product
 */

if($company = $model->seller)
    $this->params['breadcrumbs'][] = [
        'label' => $company->name,
        'url' => ['index','company'=>$company->company_id]
    ];

$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view','id'=>$model->product_id]];
$this->params['breadcrumbs'][] = ['label' => "編集"];

$createUrl = Url::to(['product-subcategory/create']);
$deleteUrl = Url::to(['product-subcategory/delete']);
$ean13 = $model->barcode;

$jscode = "
$('input[type=checkbox]').click(function() {
    if($(this).is(':checked'))
       url = '$createUrl';
    else
       url = '$deleteUrl';

    data = {
        'subcategory_id': $(this).val(),
        'ean13'         : $ean13
    };

    $.ajax({
            type: 'POST',
            url:  url,
            data: data,
            success: function(data)
            {
               $('#ajax-response').hide();
            },
            error: function(data)
            {
               $('#ajax-response').show().html('失敗しました。ページを再読込して最新の状態を確認してください');
            },
    });

});
";

$this->registerJs($jscode);
?>
<div class="product-update">

    <h1><?= Html::encode($model->name) ?></h1>

    <p class="pull-right">
        <?= Html::a('',['update','id'=>$model->product_id-1],['class'=>'btn btn-xs btn-default glyphicon glyphicon-chevron-left']) ?>
        <?= Html::a('',['update','id'=>$model->product_id+1],['class'=>'btn btn-xs btn-default glyphicon glyphicon-chevron-right']) ?>
    </p>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <H3>サブカテゴリー</h3>

    <div class="alert alert-success">
    <?php foreach(Subcategory::find()->where([
        'company_id' => $model->category->seller_id,
        'parent_id'  => null
    ])->all() as $sub): ?>

    <?= $this->render('update-subcategory',[
        'model'   => $sub,
        'actives' => ArrayHelper::getColumn($model->getSubcategories()->asArray()->all(),'subcategory_id')
    ]) ?>

    <?php endforeach ?>

    <p class="help-block">
        クリックするだけでサブカテゴリーへの追加・削除ができます
    </p>

    <p id="ajax-response" class="alert alert-warning" style="display:none"></p>

</div>
