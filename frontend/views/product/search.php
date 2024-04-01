<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/product/search.php $
 * $Id: search.php 3356 2017-05-31 14:37:32Z kawai $
 *
 * @var $this yii\web\View
 * @var $searchModel common\models\ProductSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

if(isset($breadcrumbs))
    $this->params['breadcrumbs'] = $breadcrumbs;

if(! isset($searchModel))
    $searchModel = new \frontend\models\SearchProductMaster(['customer' => $customer]);

?>
<div class="product-index">
    <h1 class="mainTitle"><?= $title ?></h1>
	<div class="col-md-2">

        <?= \frontend\widgets\SearchMenu::widget([
            'searchModel' => $searchModel,
        ]) ?>

        <?= \frontend\widgets\SearchMenuForSmartPhone::widget([
            'searchModel'    => $searchModel,
            'useSubCategory' => false
        ]) ?>
    </div>

    <?= \frontend\widgets\ProductListView::widget([
        'dataProvider' => $dataProvider,
        'grid'         => isset($grid) ? $grid : false, /* bool */
    ]) ?>

</div>
