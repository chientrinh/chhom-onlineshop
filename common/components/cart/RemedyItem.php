<?php

namespace common\components\cart;
use Yii;
use \common\models\ProductMaster;

/**
 * Item in Shopping Cart
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/cart/RemedyItem.php $
 * $Id: RemedyItem.php 3974 2018-07-31 06:52:50Z mori $
 */

class RemedyItem extends CartItem
{
    private $_name;
    private $_potency_id;
    private $_vial_id;
    private static $_company;
    private static $_category;

    public function init()
    {
        parent::init();

        $this->_type = parent::TYPE_REMEDY;
        if(! self::$_company)
            self::$_company = \common\models\Company::findOne(\common\models\Company::PKEY_HJ);

        if(! self::$_category)
            self::$_category = \common\models\Category::findOne(\common\models\Category::REMEDY);
    }

    public function attributes()
    {
        return(array_merge(parent::attributes(), ['potency_id','vial_id','prange_id']));
    }

    public function getCategory()
    {
        return self::$_category;
    }

    /* @return ActiveRecord */
    protected function getCompany()
    {
        return self::$_company;
    }

    /* @return integer */
    public function getId()
    {
        return $this->_model->remedy_id;
    }

    public function getName()
    {
        if(isset($this->_name))
            return $this->_name;

        // 商品マスタにRemedyStockのgetJancodeで返却されるバーコードをキーに表示用名称を取得する
        if(! $name = ProductMaster::find()->where(['ean13'=>$this->_model->barcode])->select('name')->scalar()) {
            // 上記$this->_model->barcode は、RemedyStockJanから、sku_idをキーにJANを取り出す処理。RemedyStockに無い商品の場合は、直接sku_idで商品マスタを検索する
            $name = ProductMaster::find()->where(['ean13'=>$this->_model->sku_id])->select('name')->scalar();
        }

        return $this->_name = $name;
    }

    /* @return integer */
    protected function getPotency_Id()
    {
        return $this->_model->potency_id;
    }

    /* @return integer */
    protected function getPrange_Id()
    {
        return $this->_model->prange_id;
    }

    public function getUrl()
    {
        return $this->_model->getUrl();
    }

    /* @return integer */
    protected function getVial_Id()
    {
        return $this->_model->vial_id;
    }

    /* @return bool */
    public function isLiquor()
    {
        return $this->_model && $this->_model->isLiquor();
    }
    
    /* @return integer */
    protected function setPrange_Id($prange_id)
    {
        $this->_model->prange_id = $prange_id;
    }

    /* @return PurchaseItem */
    public function convertToPurchaseItem($purchase_id = null, $seq = null)
    {
//var_dump($this);exit;
        return new \common\models\PurchaseItem([
            'scenario'        => \common\models\PurchaseItem::SCENARIO_REMEDY,
            'purchase_id'     => $purchase_id,
            'product_id'      => null,
            'remedy_id'       => $this->_model->remedy_id,
            'quantity'        => $this->qty,
            'price'           => $this->price,
            'unit_price'      => $this->unitPrice,
            'unit_tax'        => $this->unitTax,
            'company_id'      => $this->company->company_id,
            'code'            => $this->_model->barcode,
            'name'            => $this->getName(),
            'discount_rate'   => $this->discountRate,
            'discount_amount' => $this->discountAmount,
            'point_rate'      => $this->pointRate,
            'point_amount'    => $this->pointAmount,
            'campaign_id'      => $this->campaign_id,
            'seq'             => $seq
          //'parent'          => $this->_model->parent,
        ]);
    }

    public function setModel($model)
    {
        parent::setModel($model);

        if(! $model instanceof \common\models\RemedyStock)
            Yii::error(sprintf('internal error, %s is specified for RemedyItem, expecting RemedyStock', get_class($model)), self::className().'::'.__FUNCTION__);
    }

    public function getBarCode()
    {
        return $this->_model->barcode;
    }
}

