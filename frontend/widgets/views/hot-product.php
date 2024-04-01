<?php
use yii\helpers\Html;

/**
 * ListView content for Product at site/index
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/widgets/views/hot-product.php $
 * $Id: hot-product.php 2309 2016-03-27 01:08:03Z mori $
 *
 * $model \common\models\Product
 */

try
{
   $img_src = $model->images[0]->url;
}
catch (\Exception $e)
{
   $img_src = \yii\helpers\Url::to('@web/img/default.jpg');
}

?>

<div class="col-md-4">

    <h3>
    <div class="center_img thum_box thum_img">
    <div><?= Html::a(Html::img(\yii\helpers\Url::to($img_src), ['alt'=>$model->name, 'title'=>$model->name]).'<span class="centering"></span>',['/product/view','id'=>$model->product_id]) ?></div>
    </div>
    </h3>
    <p>
    <?= Html::a($model->company->name,['/'.$model->company->key],['class'=>'small','style'=>'color:#999']) ?><br>
    <?= Html::a($model->name,['/product/view','id'=>$model->product_id]) ?>
    </p>

</div>
