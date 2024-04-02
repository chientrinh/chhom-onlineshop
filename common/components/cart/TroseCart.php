<?php

namespace common\components\cart;
use Yii;
use common\models\Branch;
use common\models\Company;
use common\models\Payment;

/**
 * Instance of Cart, designed for a Consumer
 *
 * $URL: http://test-webhj.homoeopathy.co.jp:8000/svn/MALL/common/components/cart/ConsumerCart.php $
 * $Id: ConsumerCart.php 1416 2015-08-29 12:43:23Z mori $
 */

class TroseCart extends Cart
{
    public function init()
    {
        parent::init();
        
        $this->company = Company::findOne(Company::PKEY_TROSE);

        $branch = Branch::findOne(Branch::PKEY_TROSE);
        $this->setBranch($branch);
    }

    /**
     * @inheritdoc
     */
    protected function initPayment()
    {
        $this->payments = [
            Payment::PKEY_POSTAL_COD,
        ];

        $this->_purchase->payment_id = Payment::PKEY_POSTAL_COD;
    }

    public function updateGift($bool)
    {
        // TROSE カートではゆうメール代引きのみ受け付ける、よってギフト発送は不可
        return false;
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
