<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

use \common\models\ProductMaster;
use \common\models\ProductSubcategory;
use \common\models\Subcategory;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/views/category/_subcategory.php $
 * $Id: _subcategory.php 2429 2016-04-14 08:31:23Z mori $
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

$sub_id = Yii::$app->request->get('subcategory_id', null);
$cat_id = ArrayHelper::getColumn($categories, 'category_id');

$q1 = ProductMaster::find()->where(['category_id' => $cat_id]);
$q2 = ProductSubcategory::find()->where(['ean13'  => $q1->select('ean13')]);
$q3 = Subcategory::find()->andWhere(['subcategory_id' => $q2->select('subcategory_id')]);
$q4 = Subcategory::find()->orWhere(['subcategory_id' => $q2->select('subcategory_id')])
                                        ->orWhere(['subcategory_id' => $q3->select('parent_id')])
                                        ->andWhere(['restrict_id'   => 0])
                                        ->orderBy(['company_id'=>SORT_ASC,'parent_id'=>SORT_ASC,'weight'=>SORT_DESC]);


foreach($categories as $category)
{
    $query = clone($q4);
    $query->andWhere(['company_id'=>$category->seller_id]);

    if(0 == $query->count())
        continue;

    echo \frontend\widgets\SubcategoryMenu::widget([
        'title'   => $category->seller->name,
        'company' => $category->seller,
        'sub_id' => Yii::$app->request->get('subcategory_id'),
        'seeds'  => ArrayHelper::getColumn($query->all(), 'subcategory_id'),
    ]);
}
