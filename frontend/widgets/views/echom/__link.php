<?php
use yii\helpers\Html;

use common\models\Company;
use common\models\Branch;
use common\models\Subcategory;
use common\models\Stock;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/widgets/views/__link.php $
 * $Id: __link.php 2672 2016-07-08 02:55:07Z mori $
 *
 * $model       common\models\ProductMaster
 * $remedyStock bool
 */

$link = null;

if($model->product_id)
{
    // default link
    $link = Html::a('カートに入れる',
                    ['/cart/default/add', 'pid'=> $model->product_id],
                    ['class'=>'btn btn-warning']);


    // some TROSE products need to specify SIZE and/or COLOR before purchase
    if((Company::PKEY_TROSE == $model->company_id) && ($product = $model->product))
    {
        $q1 = $product->getSubcategories()
                      ->andWhere(['or',
                                  ['parent_id' => Subcategory::PKEY_TROSE_SIZE],
                                  ['parent_id' => Subcategory::find()->select('subcategory_id')
                                                                     ->where(['parent_id' => Subcategory::PKEY_TROSE_SIZE])],
                      ]);
        $q2 = $product->getSubcategories()
                      ->andWhere(['parent_id' => \common\models\Subcategory::PKEY_TROSE_COLOR]);

        if((1 < $q1->count()) || (1 < $q2->count()))
            $link = Html::a('色柄を指定',
                            ['/product/view','id'=> $model->product_id],
                            ['class'=>'btn btn-warning']);
    }


    //　Stockテーブルから在庫を取得する
    elseif ((Company::PKEY_TY == $model->company_id) )
    {
        // 在庫チェック
        $stock = Stock::getActualQty($model->product_id);

        // sold out (in_stock = 0)
        if ($stock === 0) 
            $model->in_stock = 0;
    }

    if(! $model->in_stock)
        $link = Html::a('完売御礼',
                                ['/product/view','id'=>$model->product_id],
                                ['class'=>'btn alert-danger']);
}
elseif($model->in_stock && $model->remedy_id && $model->potency_id && $model->vial_id)
{
        $link = Html::a('カートに入れる',
                    ['/cart/remedy/add',
                     'rid'=>$model->remedy_id,
                     'pid'=>$model->potency_id,
                     'vid'=>$model->vial_id],
                    ['class'=>'btn btn-warning']);
}
?>

<?= $link ?>
