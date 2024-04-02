<?php

namespace common\components\cart;
use Yii;
use common\models\Branch;
use common\models\Company;
use common\models\CustomerGrade;
use common\models\CustomerMembership;
use common\models\Membership;
use common\models\Payment;
use common\models\Product;

/**
 * Instance of Cart, designed for a Consumer
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/cart/MixedCart.php $
 * $Id: MixedCart.php 4248 2020-04-24 16:29:45Z mori $
 */

class MixedCart extends Cart
{
    public function init()
    {
        parent::init();

        $branch = Branch::findOne(Branch::PKEY_ATAMI);
        $this->setBranch($branch);
    }

    public function rules()
    {
        $rules   = parent::rules();
        $rules[] = ['items', 'validateItems'];

        return $rules;
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

        // ライブ配信チケットサイトは口座振替対象者は口座振替、それ以外はクレジットに固定 2020/04/21 kawai
        // 口座振替使用可能な場合は、クレジットとの２択を可能とする 2020/06/12 kawai
        if('echom-frontend' == Yii::$app->id){
            if((CustomerGrade::PKEY_AA <= $customer->grade_id || $customer->isAgency()) && isset($customer->ysdAccount->detail)) {
                $payments[] = Payment::PKEY_DIRECT_DEBIT;
                $payments[] = Payment::PKEY_CREDIT_CARD;
            } else {
                $payments[] = Payment::PKEY_CREDIT_CARD;
            }

            if(! in_array($this->_purchase->payment_id, $payments))
            $this->_purchase->payment_id = array_shift(array_values($payments));

            $this->payments = $payments;

            return;
        }


        // hpfrontは口座振替使用不可
        if((CustomerGrade::PKEY_AA <= $customer->grade_id || $customer->isAgency()) && isset($customer->ysdAccount->detail) && (Yii::$app->id !== 'app-hpfront'))
            $payments[] = Payment::PKEY_DIRECT_DEBIT;
        elseif($customer->isAgency())
            $payments[] = Payment::PKEY_BANK_TRANSFER;

        // サポート注文は先送りされたため、一旦除外する 2017/09/27
        //if($customer->isAgency())
            //$payments[] = Payment::PKEY_DROP_SHIPPING;
        //else
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
        $this->purchase->setCampaign($campaign);
        if(!$this->hasErrors('campaign') && $this->purchase->campaign)
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

    /**
     * キャンペーンオブジェクトをセットする
     *
     **/
//    public function updateAgent($direct_customer)
//    {
//        $this->purchase->setAgent($direct_customer);
//        // 配送先を変更する
//        $addr = \common\models\CustomerAddrbook::findOne([
//             'customer_id' => Yii::$app->user->id,
//             'code'          => $direct_customer->code,
//        ]);
//        if(!$this->hasErrors('agent_id') && $this->purchase->agent_id) {
//            $this->setDestination($addr);
//            $this->delivery->code = $addr->code;
//            $this->purchase->setCustomer($direct_customer);
//            return true;
//        }
//        return false;
//    }

//    public function unsetAgent()
//    {
//        if(!$this->purchase->agent_id)
//            return false;
//
//        $customer = Yii::$app->user->identity;
//        $this->purchase->agent_id = null;
//        $this->setDestination($customer);
//        $this->delivery->code = "";
//        $this->purchase->setCustomer($customer);
//        return true;
//    }
//    
    
    public function validateItems($attr, $params)
    {
        $qty = 0;
        foreach($this->items as $item)
        {
            if(! $model = $item->model)
                continue;

            if(! $model instanceof Product)
                continue;

            if(in_array($model->product_id, [Product::PKEY_TORANOKO_G_ADMISSION,
                                             Product::PKEY_TORANOKO_N_ADMISSION])
            ){
                $qty += $item->quantity;

                $user = Yii::$app->user->identity;
                if(! $user)
                    $this->addError($attr, "「{$model->name}」はご注文できません。ログインしたお客様のみ購入可能です");

                else
                {
                    $q = CustomerMembership::find()->toranoko()
                                                   ->andWhere(['customer_id' => $user->id])
                                                   ->andWhere(['>','expire_date', new \yii\db\Expression('DATE_ADD(NOW(), INTERVAL 1 YEAR)')]); // 365 日以上未来の会員権がある

                    if($q->exists())
                        $this->addError($attr, sprintf("とらのこ会員は %s まで有効につき「%s」のご購入は不要です",
                                                       date('Y-m-d', strtotime($q->max('expire_date'))),
                                                       $model->name));
                }
            }

            elseif($model->product_id == Product::PKEY_TORANOKO_N_UPGRADE)
            {
                $qty += $item->quantity;

                $user = Yii::$app->user->identity;
                if(! $user || ! $user->getMemberships()->andWhere([
                    'membership_id' => [Membership::PKEY_TORANOKO_NETWORK,
                                        Membership::PKEY_TORANOKO_NETWORK_UK]])
                                     ->exists()
                )
                    $this->addError($attr, "「{$model->name}」はご注文できません。現在とらのこネットワーク会員のお客様のみ購入可能です");
            }
        }
        if(1 < $qty)
            $this->addError($attr, "年会費が $qty 点指定されていますが、ご購入は 1 点のみでお願いします");

        if(0 < $qty)
            if(Payment::PKEY_DROP_SHIPPING == $this->_purchase->payment_id)
                $this->addError($attr, "年会費を含むご注文では代行発送を指定できません");

        return $this->hasErrors($attr);
    }
}
