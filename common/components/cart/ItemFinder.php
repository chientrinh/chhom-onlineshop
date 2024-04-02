<?php

namespace common\components\cart;
use Yii;

/**
 * finder of CartItem
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/cart/ItemFinder.php $
 * $Id: ItemFinder.php 3639 2017-10-04 03:23:20Z kawai $
 */

class ItemFinder extends \yii\base\Model
{
    private $company;
    private $branch;
    private $scenario;
    private $code;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * 商品（単品商品、レメディー商品）情報検索
     *
     * @params int   $id      商品ID
     * @params array $options 検索条件
     *
     * @return mixed (ProductItem | RemedyItem | null)
     */
    public function find($id, $options=[])
    {
        $item = null;

        // 容器IDがある場合は、レメディー商品検索
        if(isset($options['vial_id']))
            return $this->findRemedy($id, $options);

        return $this->findProduct($id, $options);
    }

    /**
     * 商品検索（単品商品）
     *
     * @param int   $id      商品ID
     * @param array $options 検索条件
     *
     * @return mixed (ProductItem | null)
     */
    private function findProduct($id, $options=[])
    {
        $recipe_id = null;

        if(isset($options['recipe_id']))
            $recipe_id = $options['recipe_id'];

        $product = \common\models\Product::findOne($id);
        if(! $product)
        {
            Yii::warning(sprintf('product_id not found: (%s)', $id), self::className().'::'.__FUNCTION__);
            return null;
        }
        if(isset($options['name']))
            $product->name = $options['name']; // オプション指定商品（特別レメディー、トミーローズ商品）

        $product_item =  new ProductItem(['model'=>$product]);
        $product_item->recipe_id =$recipe_id;
        return $product_item;
    }

    /**
     * 商品検索（単品商品）
     *
     * @param int   $id      商品ID
     * @param array $options 検索条件
     *
     * @see RemedyStock
     * @see RemedyItem
     *
     * @return mixed (ProductItem | null)
     */
    private function findRemedy($id, $options)
    {
        $recipe_id = null;

        if(isset($options['recipe_id']))
            $recipe_id = $options['recipe_id'];

        $condition = [
            'remedy_id' => $id,
            'vial_id'   => $options['vial_id'],
            'potency_id'=> $options['potency_id'],
        ];
        if(isset($options['prange_id']))
            $condition['prange_id'] = $options['prange_id'];

        $stock = \common\models\RemedyStock::find()->where($condition)->one();
        if(! $stock)
        {
            // not in stock, it must be 滴下単品レメディー
            // コンビネーションレメディーの作成ではここを通らない前提で、「在庫有り」とみなして作成する
            $condition['in_stock'] = 1;
            $stock = new \common\models\RemedyStock($condition);
            $stock->scenario = $stock::SCENARIO_COMPOSE;
            if(! $stock->validate())
            {
                Yii::warning($stock->errors, self::className().'::'.__FUNCTION__);
            }
        }
        $remedy_item = new RemedyItem(['model'=>$stock]);
        $remedy_item->recipe_id = $recipe_id;
        return $remedy_item;
    }

}

