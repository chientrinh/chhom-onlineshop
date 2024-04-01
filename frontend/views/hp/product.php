<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/hp/product.php $
 * $Id: product.php 4067 2018-11-28 08:10:14Z kawai $
 *
 * @var $this         yii\web\View
 * @var $title        string for <title>
 * @var $h1           string for <h1>
 * @var $breadcrumbs  array
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel  common\models\ProductSearch
 * @var $grid         bool: render GridView or not
 */

$this->title                 = $title;
$this->params['body_id']     = 'Search';

?>
<div class="product-index">

    <p class="pull-right"><?= Html::a("会社概要",[sprintf('/%s/index',$this->context->company->key)]) ?></p>

    <h1 class="mainTitle"><?= $h1 ?></h1>

	<div class="col-md-2">
        <?= \frontend\widgets\SearchMenu::widget([
            'company'     => $this->context->company,
            'searchModel' => $searchModel,
            'submenu'     => ['book'],
        ]) ?>
        
        <?= \frontend\widgets\SearchMenuForSmartPhone::widget([
            'company'     => $this->context->company,
            'searchModel' => $searchModel,
            'submenu'     => ['book'],
        ]) ?>
	</div>

    <?= \frontend\widgets\ProductListView::widget([
        'dataProvider' => $dataProvider,
        'searchModel'  => $searchModel,
        'grid'         => isset($grid) ? $grid : false, /* bool */
    ]) ?>

</div>
