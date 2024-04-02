<?php

namespace common\components\cart;
use Yii;
use common\models\Branch;
use common\models\Company;
use common\models\CustomerGrade;
use common\models\Payment;

/**
 * Instance of Cart, designed for a Consumer
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/cart/TyCart.php $
 * $Id: TyCart.php 4242 2020-03-20 05:15:48Z mori $
 */

class TyCart extends Cart
{
    public function init()
    {
        parent::init();
        
        $this->company = Company::findOne(Company::PKEY_TY);

        $branch = Branch::findOne(Branch::PKEY_ROPPONMATSU);
        $this->setBranch($branch);
    }

    /**
     * @inheritdoc
     */
    protected function initPayment()
    {
        if(! $customer = $this->customer)
        {
            $pid = Payment::PKEY_YAMATO_COD;

            $this->payments = [ $pid ];
            $this->_purchase->payment_id = $pid;
            return;
        }

        $payments = [];

        if((CustomerGrade::PKEY_AA <= $customer->grade_id || $customer->isAgency()) && isset($customer->ysdAccount->detail))
            $payments[] = Payment::PKEY_DIRECT_DEBIT;
        elseif($customer->isAgency())
            $payments[] = Payment::PKEY_BANK_TRANSFER;

        //if($customer->isAgency())
        //    $payments[] = Payment::PKEY_DROP_SHIPPING;
        //else
        // サポート注文は先送りとなったため、一旦除外する 2017/09/29
        //if(!$customer->isAgency())
            $payments[] = Payment::PKEY_YAMATO_COD;

        if(! in_array($this->_purchase->payment_id, $payments))
            $this->_purchase->payment_id = array_shift(array_values($payments));

        $this->payments = $payments;
        return;
    }

    public function setCustomer(\common\models\Customer $customer)
    {
        parent::setCustomer($customer);

        $this->initPayment();
    }

    /**
     * キャンペーンオブジェクトをセットする
     *
     **/
    public function updateCampaign($campaign)
    {
        $this->_purchase->campaign = $campaign;
        if(!$this->errors)
            return true;
        return false;
    }

    public function unsetCampaign()
    {
        if(!$this->purchase->campaign)
            return false;

        $this->purchase->campaign = null;
        $this->purchase->campaign_id = null;
        return true;
    }

}
