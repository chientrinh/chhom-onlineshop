<?php

namespace common\components\cart;
use Yii;
use \common\models\Company;

/**
 * Instance of Cart, designed for a Wholeseller
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/cart/WholesellerCart.php $
 * $Id: WholesellerCart.php 1161 2015-07-18 03:31:02Z mori $
 */

class WholesellerCart extends Cart
{
    public function init()
    {
        parent::init();

        $this->payments = \common\models\Payment::findAll([
            \common\models\Payment::PKEY_BANK_TRANSFER,
            \common\models\Payment::PKEY_DROP_SHIPPING,
        ]);

        if(! $this->_purchase->payment_id)
            $this->setPayment(\common\models\Payment::PKEY_BANK_TRANSFER);
    }

    public function compute()
    {
        // clear up all point just in case the customer has any CustomerGrade
        foreach($this->_items as $k => $item)
        {
            if(0 < $item->pointTotal)
                $this->_items[$k]->point = new NullItemPoint();
        }

        return parent::compute();
    }

}
