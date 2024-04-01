<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/product/search.php $
 * $Id: search.php 2972 2016-10-15 05:36:04Z mori $
 *
 * @var $this yii\web\View
 * @var $searchModel common\models\ProductSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->params['breadcrumbs'] = [];
if(isset($searchModel->keywords) || $searchModel->keywords != '')
    $this->params['breadcrumbs'][] = ['label' => $searchModel->keywords];



if(! isset($searchModel))
    $searchModel = new \frontend\models\SearchProductMaster(['customer' => $customer]);

?>
<div class="product-index">
    <h1 class="mainTitle"><?= $title ?></h1>
	<!-- <div class="col-md-2">

        <?php //echo \frontend\widgets\SearchMenu::widget([
            //'searchModel' => $searchModel,
        //]) ?>

        <?php
         //echo  \frontend\widgets\SearchMenuForSmartPhone::widget([
        //     'searchModel'    => $searchModel,
        //     'useSubCategory' => false
        // ]) ?>
    </div> -->

    <?= \frontend\widgets\ProductListView::widget([
        'dataProvider' => $dataProvider,
        'grid'         => isset($grid) ? $grid : false, /* bool */
    ]) ?>

</div>
