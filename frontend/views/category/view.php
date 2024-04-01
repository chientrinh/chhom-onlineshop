<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use \common\models\Subcategory;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/category/view.php $
 * $Id: view.php 4067 2018-11-28 08:10:14Z kawai $
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

$submenu = [];

if(preg_match('/レメディー/u', $h1))
    $submenu[] = 'remedy';

if(preg_match('/化粧品/u', $h1))
    $submenu[] = 'cosmetic';

if(preg_match('/書籍/u', $h1))
    $submenu[] = 'book';

?>

<div class="category-view">

    <div class="body-content">

    <div id="Home">
        <?= \frontend\widgets\CategoryNav::widget([
            'category' => current($categories),
        ]) ?>
    </div>

    <h1 class="mainTitle"><?= $h1 ?></h1>

    <div class="col-md-2">
        <?= \frontend\widgets\SearchMenu::widget([
            'action'      => ['/product/search'],
            'searchModel' => $searchModel,
            'submenu'     => $submenu,
        ]) ?>
        
        <?= \frontend\widgets\SearchMenuForSmartPhone::widget([
            'action'      => ['/product/search'],
            'searchModel' => $searchModel,
            'submenu'     => $submenu,
            'categories'  => $categories,
        ]) ?>

        <?php if(! preg_match('/レメディー/u', $h1)): ?>
        <div class="product-search">
            <div class="inner">
                <?= $this->render('_subcategory',['categories'=>$categories]) ?>
            </div>
        </div>
        <?php endif ?>

    </div>

    <?= \frontend\widgets\ProductListView::widget([
        'dataProvider' => $dataProvider,
        'grid'         => isset($grid) ? $grid : false,
    ]) ?>
    </div>
</div>
