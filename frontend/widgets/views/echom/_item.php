<?php
use yii\helpers\Html;
use yii\helpers\Url;

use common\models\Company;
use common\models\Subcategory;

/**
 * $URL: http://tarax.toyouke.com/svn/MALL/frontend/widgets/views/_item.php $
 * $Id: _item.php 3221 2017-03-17 11:08:34Z kawai $
 */
$formatter = new \yii\i18n\Formatter();

$img_src = null;
$modelName  = $model->name;
$modelPrice = $formatter->asCurrency($model->price);

$images = $model->images;
if($images)
{
    $one = array_shift($images);
    $img = Html::img($one->url, ['alt' => $one->caption]);
}
else
{
    $default_img = "default.jpg";


    // 商品がレメディーである場合のみ白ラベル写真。アルポの場合はアルポ写真。チンクチャー、フラワーエッセンスは通常商品と同じ画像を使う。
    if($model->isRemedy()) {
        if($model->vial_id < 5) {
            $default_img = "default_remedy.jpg";
        } else if($model->vial_id == 5) {
            $default_img = "default_alpo.jpg";
        }
    }
    
    $img = Html::img('@web/img/'.$default_img, ['alt' => $model->name]);

}
// 商品がレメディーであり、かつレメディーストックにない商品、またはレメディー以外でvidがパラメータにセットされていない状態ならパラメータを追加する
if(($model->isRemedy() && $model->in_stock == -1) || (isset($model->vial_id) && $model->vial_id > 4 && strpos($model->url, "vid=") == false)) {

    $url = $model->url.'?vid='.$model->vial_id.'#'.\common\models\RemedyPotency::findOne($model->potency_id)->name;

} else {
     // 通常の商品
     $url = $model->url;
}

// 終了日が過ぎた商品は表示させない
$active_product = true;
if ($model->product_id && !$model->product->isActive()) {
    $active_product = false;
}
?>

<?php if ($active_product): ?>
<div class="col-md-4">
  <h3>
    <div class="center_img thum_box thum_img">
      <div>
        <a href="<?= $url ?>">
          <?= $img ?>
          <span class="centering"></span>
        </a>
      </div>
    </div>
  </h3>

  <div style="height:15em">
  <p><?= Html::a($modelName, $url) ?></p>
  <p class="Feature"><?= $model->excerpt ?></p>
  </div>
</div>
<?php endif; ?>