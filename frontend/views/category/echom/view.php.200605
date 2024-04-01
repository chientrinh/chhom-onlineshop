<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use \common\models\Subcategory;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/category/view.php $
 * $Id: view.php 2923 2016-10-05 07:41:02Z mori $
 *
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

if($model = Subcategory::findOne(Yii::$app->request->get('subcategory_id')))
{
    $this->params['breadcrumbs'][] = [
        'label' => $model->fullname,
        'url'   => Url::current(['subcategory_id'=>$model->subcategory_id])
    ];
    $this->params['breadcrumbs'][] = [
        'label' => sprintf('(%s)', $model->company->name),
        'url'   => [sprintf('/%s', $model->company->key)],
    ];
}


?>

<div class="category-view">

    <div class="body-content">
<br />
    <h1 class="mainTitle"><?= $h1 ?></h1>

    <?= \frontend\widgets\ProductListView::widget([
        'dataProvider' => $dataProvider,
        'grid'         => isset($grid) ? $grid : false,
    ]) ?>
    </div>
</div>
