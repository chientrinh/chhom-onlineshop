<?php

namespace common\components\cart;

use Yii;
use \common\models\Product;

/**
 * Item in Shopping Cart
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/cart/ProductItem.php $
 * $Id: ProductItem.php 2717 2016-07-15 03:12:07Z naito $
 */

class ProductItem extends CartItem
{
    public $tax;

    public function init()
    {
        parent::init();

        $this->_type = parent::TYPE_PRODUCT;
    }

    /* @return ActiveRecord */
    protected function getCompany()
    {
        return $this->_model->seller;
    }

    public function getUrl()
    {
        return \yii\helpers\Url::to(['/product/view','id'=>$this->model->product_id]);
    }

    /* @return bool */
    public function isLiquor()
    {
        return $this->_model && $this->_model->isLiquor();
    }

    public function getTaxRate()
    {
        return $this->isLiquor() ? \common\models\Tax::findOne(1)->getRate() : \common\models\Tax::findOne(2)->getRate();
    }

    public function setTaxRate($vol)
    {
        $this->tax_rate = $vol;
    }

    public function setPrice($val)
    {
        $this->price = $val;
    }

    public function setTax($val)
    {
        $this->tax = $val;
    }

    public function setUnitPrice($val)
    {
        $this->unit_price = $val;
    }

    public function setUnitTax($val)
    {
        $this->unit_tax = $val;
    }

    /* @return PurchaseItem */
    public function convertToPurchaseItem($purchase_id = null, $seq = null)
    {
        // 該当商品が書籍であればisbnを、そうでなければJANコードをdtb_product_janから取得できればJANコード、どちらもなければ商品番号をcodeにセットする
        $code = "";
        if($this->_model->isBook() && $this->_model->bookinfo) {
            $code = $this->_model->bookinfo->isbn;
        }
        if($code == "") {

            if($this->_model->productJan) {
                $code = $this->_model->productJan->jan;
            } else {
                $code = $this->_model->code;
            }
        }
    

        return new \common\models\PurchaseItem([
            'scenario'        => \common\models\PurchaseItem::SCENARIO_PRODUCT,
            'purchase_id'     => $purchase_id,
            'product_id'      => $this->_model->product_id,
            'quantity'        => $this->qty,
            'price'           => $this->price,
            'unit_price'      => $this->unitPrice,
            'unit_tax'        => $this->unitTax,
            'company_id'      => $this->company->company_id,
            'code'            => $code,
            'name'            => $this->_model->name,
            'discount_rate'   => $this->discountRate,
            'discount_amount' => $this->discountAmount,
            'point_rate'      => $this->pointRate,
            'point_amount'    => $this->pointAmount,
            'point_consume'    => $this->pointConsume,
            'point_consume_rate'    => $this->pointConsumeRate,
            'campaign_id'     => $this->campaign_id,
            'is_wholesale'    => $this->is_wholesale,
            'seq'             => $seq,
            'minus_product'   => ($this->price < 0) ? 1 : 0
        ]);
    }

    public function setModel($model)
    {
        parent::setModel($model);

        if('common\models\Product' !== get_class($model))
            Yii::error(sprintf('internal error, %s is specified for ProductItem', get_class($model)));
    }

    public function getBarCode()
    {
        // 該当商品が書籍であればisbnを、そうでなければJANコードをdtb_product_janから取得できればJANコード、どちらもなければ商品番号をcodeにセットする
        $barcode = "";
        if($this->_model->isBook() && $this->_model->bookinfo) {
            $barcode = $this->_model->bookinfo->isbn;

        } else {
            
            if($this->_model->productJan) {
                $barcode = $this->_model->productJan->jan;
            } else {
                $barcode = $this->_model->getBarCode();
            }
        }

        return $barcode;
    }
}

