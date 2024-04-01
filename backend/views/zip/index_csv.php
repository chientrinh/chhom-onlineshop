<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/zip/index_csv.php $
 * $Id: index_csv.php 2667 2016-07-07 08:26:14Z mori $
 *
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

?>
<div class="product-pickcode-index">

    <h1><?= Html::encode($this->title) ?></h1>

ean13,product_code,pickcode,model.name<br>
<?php foreach($dataProvider->models as $model): ?>
<?= implode(',', [
    $model->zipcode,
    ($p = $model->pref) ? $p->name : null,
    $model->city,
    $model->town,
    $model->yamato_22,
    $model->spat,
]) ?><br>
<?php endforeach ?>

</div>
