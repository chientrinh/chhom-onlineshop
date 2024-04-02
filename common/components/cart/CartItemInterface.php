<?php

namespace common\components\cart;
use Yii;

/**
 * Abstract of Item in Shopping Cart
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/cart/CartItemInterface.php $
 * $Id: CartItemInterface.php 1748 2015-11-01 22:36:58Z mori $
 */

interface CartItemInterface
{
    // public function compare($target);

    // public function getBasePrice();

    // public function getCategory();

    public function getCharge();

    // public function getCode();

    public function getCompany();

    public function getDiscountAmount();

    public function getDiscountRate();

    public function getDiscount();

    public function getDiscountLabel();

    public function getId();

    public function getImage();

    public function getModel();

    public function getName();

    public function getPoint();

    public function getPointAmount();

    public function getPointRate();

    public function getPointTotal();

    // public function getProduct_id();

    // public function getPrice();

    // public function getQty();

    // public function getType();

    public function getUrl();

    // public function increase($qty);

    public function isLiquor();

    public function isProduct();

    public function isRemedy();

    // public function setDiscount($customer, $time=null);

    // public function setQty($num);

    // public function save($purchase_id, $seq);
}

