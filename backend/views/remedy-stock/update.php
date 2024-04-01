<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use \common\models\Subcategory;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/remedy-stock/update.php $
 * $Id: update.php 3268 2017-04-21 01:45:07Z kawai $
 *
 * @var $this yii\web\View
 * @var $model common\models\RemedyStock */

$this->params['breadcrumbs'][] = ['label' => $model->remedy->name, 'url' => ['/remedy/viewbyname', 'name' => $model->remedy->name]];
$this->params['breadcrumbs'][] = ['label' => '既製品レメディー', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view',
                                                                     'remedy_id'  => $model->remedy_id,
                                                                     'potency_id' => $model->potency_id,
                                                                     'vial_id'    => $model->vial_id ]];
$this->params['breadcrumbs'][] = ['label' => '編集'];

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
<div class="remedy-stock-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<h3>画像</h3>

<div class="row">

    <?php if($model->images) foreach($model->images as $image): ?>
        <div class="col-xs-6 col-md-3">
            <span class="thumbnail">
                <?= Html::a(' ',['product-image/delete','id'=>$image->img_id],[
                    'class'=>'btn btn-xs btn-danger glyphicon glyphicon-remove pull-right',
                    'title'=>'画像を削除します',
                    'data' =>['confirm' => "画像を削除していいですか"],
                ]) ?>
                <?= Html::img($image->url, ['alt'=> $image->basename, 'style'=>'max-width:100px;max-height:100px']) ?>
            </span>
        </div>
    <?php endforeach ?>

    <div class="col-xs-6 col-md-3">
        <span class="thumbnail pull-left">
            <?= Html::a(' ',['product-image/add','id'=>$model->code],['class'=>'btn btn-xs btn-success glyphicon glyphicon-plus pull-center','title'=>'画像を追加します']) ?>
        </span>
    </div>

</div>

<H3>サブカテゴリー</h3>

<div class="alert alert-success">

    <?php foreach(Subcategory::find()->where([
        'company_id' => $model->category->seller_id,
        'parent_id'  => null
    ])->all() as $sub): ?>

    <?= $this->render('update-subcategory',[
        'model'   => $sub,
        'actives' => ArrayHelper::getColumn($model->subcategories,'subcategory_id')
    ]) ?>

    <?php endforeach ?>

    <p class="help-block">
        クリックするだけでサブカテゴリーへの追加・削除ができます
    </p>

    <p id="ajax-response" class="alert alert-warning" style="display:none"></p>

</div>
