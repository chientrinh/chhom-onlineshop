<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/widgets/views/search-menu-item.php $
 * $Id: search-menu-item.php 1651 2015-10-12 16:03:16Z mori $
 */

try
{
   $img_src = $model->images[0]->url;
}
catch (\Exception $e)
{
   $img_src = Url::to(\common\models\ProductImage::DEFAULT_URL);
}

$formatter = new \yii\i18n\Formatter();
?>  

<div class="col-md-4">

  <h3>
    <div class="center_img thum_box thum_img">
      <div>
        <a href="<?= $model->url ?>">
          <?= Html::img($img_src, ['alt'=>$model->name]) ?>
          <span class="centering"></span>
        </a>
      </div>
    </div>
  </h3>

  <p><?= Html::a($model->company->name,['/'.$model->company->key],['class'=>'small','style'=>'font-size:50%;color:#999']) ?></p>
  <p><?= Html::a($model->name, $model->url) ?></p>
  <p class="Feature"><?= $model->excerpt ?></p>
  <p class="Price"><?= $model->price ? $formatter->asCurrency($model->price) : '' ?></p>

</div>
