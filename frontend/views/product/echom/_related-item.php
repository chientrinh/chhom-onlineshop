<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * $URL: http://tarax.toyouke.com/svn/MALL/frontend/views/product/_related-item.php $
 * $Id: _related-item.php 3222 2017-03-17 11:26:05Z kawai $
 *
 * $this  yii\base\View
 * $model common\models\ProductMaster
 */

    $default_img = "@web/img/default.jpg";
    if($model->isRemedy()) {
        $default_img = "@web/img/default_remedy.jpg";
    }

?>

<li class="slide" data-key="<?= $model->ean13 ?>">

    <div class="center_img thum_box thum_img">
        <a href="<?= $model->url ?>">
            <?= Html::img(Url::to(($img = $model->image) ? $img->url : $default_img),['alt' => $model->name ]) ?>
        </a>
    </div>

    <p class="Name"><?= Html::a($model->name, $model->url)?></p>
    <p class="Price">￥<?= number_format($model->price) ?></p>

</li>
