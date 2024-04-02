<?php

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/cms/views/oasis/_item.php $
 * $Id: _item.php 2921 2016-10-05 06:47:10Z mori $
 *
 * @var $this  \yii\base\View
 * @var $model \common\models\Product
 */

use \yii\helpers\Html;
use \yii\helpers\Url;

?>

<div class="col-md-3 col-sm-4 col-xs-6 text-center">

  <h3>
    <div class="center_img thum_box thum_img">
      <div>
          <a href="<?= Url::to($model->url) ?>" target="_blank">
            <?= Html::img(['view','id'=>$model->id,'page'=>'books/images/cover.jpg']) ?>
          <span class="centering"></span>
        </a>
      </div>
    </div>
  </h3>

  <div>
      <p><?= Html::a($model->id, $model->url, ['target'=>'_blank']) ?></p>

      <?php if($model->pdf): ?>
          <p><?= Html::a(Html::img('@web/img/application-pdf.png'), ['view','id'=>$model->id, 'page' => $model->pdf], ['target'=>'_blank']) ?></p>
      <?php endif ?>

  </div>

</div>

