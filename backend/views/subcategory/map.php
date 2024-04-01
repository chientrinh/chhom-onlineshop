<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/views/subcategory/map.php $
 * $Id: map.php 2019 2016-01-28 08:09:29Z mori $
 *
 * @var $this yii\web\View
 * @var $company common\models\Company
 * @var $dataProvider
 */

?>

<div class="col-md-12">

<?= Html::tag('h2', $company->name) ?>
    <div class="col-xs-1">
        <?= Html::a('追加',['create','company_id'=>$company->company_id],['class'=>'btn btn-xs btn-success']) ?>
    </div>
    <div class="col-xs-11">
    <?= \yii\widgets\ListView::widget([
        'dataProvider' => $dataProvider,
        'layout'       => '{items}',
        'itemView'     => '_item',
        'options' => ['class'=>'list-view col-md-9 product-search-list'],
    ]) ?>
    </div>
</div>
