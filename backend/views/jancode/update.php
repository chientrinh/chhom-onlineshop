<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/jancode/update.php $
 * $Id: update.php 2316 2016-03-27 06:18:47Z mori $
 *
 * @var $this yii\web\View
 * @var $model common\models\Product
 * @var $model ( ProductJan | RemedyStockJan )
 */

use yii\helpers\Html;

$this->params['breadcrumbs'][] = ['label' => '編集'];

?>
<div class="product-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
