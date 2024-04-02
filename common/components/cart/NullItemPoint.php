<?php

namespace common\components\cart;
use Yii;

/**
 * container of Point for CartItem
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/cart/NullItemPoint.php $
 * $Id: NullItemPoint.php 1117 2015-06-30 16:31:16Z mori $
 */

class NullItemPoint extends ItemPoint
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

