<?php

namespace common\components\cart;
use Yii;

/**
 * container of Point for CartItem
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/cart/NullItemDiscount.php $
 * $Id: NullItemDiscount.php 1022 2015-05-17 09:35:23Z mori $
 */

class NullItemDiscount extends ItemDiscount
{
    public function getRate()
    {
        return 0;
    }

    public function getAmount()
    {
        return 0;
    }

    public function setRate($amount)
    {
        return false; // setRate() failed
    }

    public function setAmount($amount)
    {
        return false; // setAmount() failed
    }

}

