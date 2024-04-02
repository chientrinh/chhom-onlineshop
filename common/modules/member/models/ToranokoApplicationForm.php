<?php

namespace common\modules\member\models;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/member/models/ToranokoApplicationForm.php $
 * $Id: ToranokoApplicationForm.php 3188 2017-02-08 05:30:13Z naito $
 */

use Yii;
use \yii\helpers\ArrayHelper;
use \common\models\Branch;
use \common\models\Customer;
use \common\models\CustomerMembership;
use \common\models\Membercode;
use \common\models\Membership;
use \common\models\Payment;
use \common\models\Product;
use \common\models\PointingForm;
use \common\models\PointingItem;
use \common\models\PurchaseForm;
use \common\models\PurchaseItem;
use \common\components\sendmail\ToranokoApplicationMail;

class ToranokoApplicationForm extends \yii\base\Model
{
    const EXPIRE_MONTH = 5;
    const EXPIRE_DAY   = 4;

    public $customer;
    public $membercode;
    public $product_id;
    public $issues;
    public $payment_id;
    public $paid;
    public $shipped;
    public $pointBack;

    public $branch;  /* @var Branch   */
    public $seller;  /* @var Customer */

    private $_pointing;   /* @var Pointing earned by Customer */
    private $_purchase;   /* @var Purchase (of Agency or Customer) */
    private $_membership; /* @var CustomerMembership of Customer */

    public function init()
    {
        parent::init();

        if(! isset($this->pointBack))
            throw new \yii\base\InvalidConfigException('pointBack is not set');
    }

    public function rules()
    {
        return [
            [['branch'],     'default', 'value' => Branch::findOne(Branch::PKEY_HE_TORANOKO) ],
            [['product_id'], 'default', 'value' => Product::PKEY_TORANOKO_G_ADMISSION /* とらのこ正会員 年会費 */ ],
            [['payment_id'], 'exist', 'targetClass' => Payment::className(),'skipOnEmpty' => false ],
            [['product_id'], 'exist', 'targetClass' => Product::className(),'skipOnEmpty' => false ],
            [['membercode'], 'exist', 'targetClass' => Membercode::className(),'targetAttribute' => 'code' ],
            [['membercode'], 'required', 'when'=>function($model){ return ! isset($model->customer);   }],
            [['customer'],   'required', 'when'=>function($model){ return   isset($model->membercode); }],
            [['shipped','paid'], 'boolean', 'skipOnEmpty' => false ],
            ['seller', 'validateSeller', 'skipOnEmpty' => true ],
            ['membercode', 'validateMembercode', 'skipOnError' => true ],
            ['product_id', 'validateMembership', 'skipOnEmpty' => true ],
            ['issues', 'each','rule'=>['exist','targetClass'=>Product::className(),'targetAttribute'=>'product_id']],
        ];
    }

    public function apply()
    {
        if($this->hasErrors())
            return false;

        $transaction = Yii::$app->db->beginTransaction();
        try
        {
            if(! $this->saveCustomer() ||
               ! $this->saveMembership() ||
               ! $this->savePurchase() ||
               ! $this->savePointing() )
            {
                $transaction->rollBack();
                return $this->failure('shipped',"rollbackが発生しました");
            }
        }
        catch (\yii\db\Exception $e)
        {
            $transaction->rollBack();

            Yii::warning($e->__toString(), $this->className().'::'.__FUNCTION__);
            return $this->failure('paid',"rollbackが発生しました");
        }
        $transaction->commit();

        return $this->sendMail();
    }

    /* @return false */
    private function failure($attr, $message)
    {
        Yii::error($message, $this->className().'::'.__FUNCTION__);
        $this->addError($attr, $message);

        return false;
    }

    public function getMembership()
    {
        if(Product::PKEY_TORANOKO_G_ADMISSION == $this->product_id)
            return Membership::findOne(Membership::PKEY_TORANOKO_GENERIC);

        if(Product::PKEY_TORANOKO_N_ADMISSION == $this->product_id)
            return Membership::findOne(Membership::PKEY_TORANOKO_NETWORK);

        return null;
    }

    public function getProduct()
    {
        return Product::findOne($this->product_id);
    }

    private function saveCustomer()
    {
        $customer = $this->customer;
        if(! $customer && $this->membercode)
        {
            $cid = Membercode::find()->where(['code'=>$this->membercode])->scalar('customer_id');
            if($cid)
                $customer = Customer::findOne($cid);
            else
                $customer = new Customer(['name01'=>'','name02'=>'','kana01'=>'','kana02'=>'','email'=>'','addr01'=>'','addr02'=>'','tel01'=>'','tel02'=>'','tel03'=>'']);
        }
            
        if($customer->isNewRecord)
        {
            if($this->membercode)
                $customer->detachBehavior('membercode');

            if(! $customer->save(false))
                return $this->failure('customer',array_shift($customer->errors));

            if($this->membercode)
            {
                $model = Membercode::findOne(['code'=>$this->membercode,'customer_id'=>null]);
                $model->customer_id = $customer->customer_id;
                if(! $model->save())
                    return $this->failure('membercode',array_shift($model->errors));
            }
        }
        elseif(isset($this->membercode) && ($this->membercode != $customer->code))
        {
            $model = Membercode::findOne(['code'=>$this->membercode]);
            if(! \common\components\CustomerMigration::attachMembercode($customer, $model))
                return $this->failure('membercode',array_shift($model->errors));
        }

        $this->customer = $customer;
        return true;
    }
    private function getNextExpireDate($prev)
    {
        $exp = strtotime($prev);

        if($this->paid)
            $expire_date = sprintf('%04d-%02d-%02d 23:59:59',
                               ((5 <= date('m',$exp)) ? date('Y',$exp)+1 : date('Y',$exp)),
                               self::EXPIRE_MONTH,
                               self::EXPIRE_DAY);
        else
            $expire_date = date('Y-m-d H:i:s', $exp + 1);

        return $expire_date;
    }
    /* @bried insert another membership to customer */
    private function saveMembership()
    {
        if(! $this->seller)
            return true; // Purchase を INSERT する場合には以下を実行しない

        $mship = $this->membership;

        $query = $this->customer->getMemberships()
                      ->orderBy('expire_date DESC')
                      ->andWhere(['membership_id' => [Membership::PKEY_TORANOKO_GENERIC,
                                                      Membership::PKEY_TORANOKO_GENERIC_UK,
                                                      Membership::PKEY_TORANOKO_NETWORK_UK,
                                                      Membership::PKEY_TORANOKO_NETWORK]]);

        if($model = $query->one())
        {
            $expire_date = $this->getNextExpireDate($model->expire_date);

            if($model->membership_id === $mship->membership_id)
                if($this->_membership = $model->extend($expire_date))
                    return true;

            $start_date = date('Y-m-d H:i:s', strtotime($model->expire_date) + 1);
        }
        else
        {
            $start_date  = date('Y-m-d H:i:s');
            $expire_date = $this->getNextExpireDate(date('Y-m-d H:i:s'));
        }

        $model = new CustomerMembership(['customer_id'  => $this->customer->customer_id,
                                         'membership_id'=> $mship->membership_id,
                                         'start_date'   => $start_date,
                                         'expire_date'  => $expire_date]);
        $this->_membership = $model;

        if(! $model->save())
            return $this->failure('membeship_id', "CustomerMembership::save() failed");

        return ! $model->isNewRecord;
    }

    /* @brief record that Agency has sold a admission to a Customer */
    private function savePointing()
    {
        if(! $this->seller)
            return true;

        $product = $this->product;
        $model   = new PointingForm([
            'company_id' => $this->branch->company_id,
            'seller_id'  => $this->seller->customer_id,
            'customer_id'=> $this->customer->customer_id,
            'items'      => [
                new PointingItem([
                    'product_id' => $this->product_id,
                    'code'       => $product->code,
                    'name'       => $product->name,
                    'price'      => $product->price,
                    'quantity'   => 1,
                ])
            ],
        ]);
        if($this->issues)
        foreach($this->issues as $issue)
            if($p = Product::findOne($issue))
            $model->items[] = new PointingItem([
                    'product_id' => $issue,
                    'code'       => $p->code,
                    'name'       => $p->name,
                    'price'      => 0,
                    'quantity'   => 1,
                ]);
        $model->compute(false);
        $model->note = sprintf("有効期限: %s から %s まで",
                               date('Y-m-d',strtotime($this->_membership->start_date)),
                               date('Y-m-d',strtotime($this->_membership->expire_date)));
        $model->receive = $model->total_charge;

        $this->_pointing = $model;

        if(! $model->save())
            return $this->failure('customer',"failed pointing->save()");

        return true;
    }

    /* @brief record that Agency needs to pay admission to Company */
    private function savePurchase()
    {
        $item = new PurchaseItem([
            'product_id' => $this->product_id,
            'code'       => $this->product->code,
            'name'       => $this->product->name,
            'price'      => $this->product->price,
            'quantity'   => 1,
            'company_id' => $this->branch->company_id,
        ]);
        $items = [ $item ];

        if($this->issues)
        foreach($this->issues as $issue)
          if($p = Product::findOne($issue))
          {
            if(!$this->seller)
            $items[] = new PurchaseItem([
                    'product_id' => $issue,
                    'code'       => $p->code,
                    'name'       => $p->name,
                    'price'      => $p->price,
                    'quantity'   => 1,
                    'company_id' => $this->branch->company_id,
                    'discount_rate' => 100,
                ]);
          }

        if($this->seller)
            $this->generatePurchaseForAgency($items);   // app-frontend
        else
            $this->generatePurchaseForCustomer($items); // app-backend

        $model = $this->_purchase;

        if(! $model->save())
            return $this->failure("seller", "Purchaseの保存に失敗しました");

        return ! $model->isNewRecord;
    }

    /**
     * @brief 代理店に対する売上を起票する
     * @return void
     */
    private function generatePurchaseForAgency($items)
    {
        $model = new PurchaseForm([
            'customer_id'=> $this->seller->customer_id,
            'shipped'    => (int) true,
            'paid'       => (int) false,
            'payment_id' => Payment::PKEY_BANK_TRANSFER,

            'branch_id'  => $this->branch->branch_id,
            'company_id' => $this->branch->company_id,
            'items'      => $items,
        ]);
        $model->compute(false);

        if($model->postage)
        {
            $model->total_charge -= $model->postage;
            $model->postage       = 0;
        }
        $model->point_given = $this->pointBack;
        $model->note        = "{$this->customer->kana} 様 お申し込み分";

        if(($ysd = $this->seller->ysdAccount) && $ysd->isValid())
            $model->payment_id = Payment::PKEY_DIRECT_DEBIT; // 口座振替

        $model->detachBehavior('toranoko'); // do not create Membership from Pruchase

        $this->_purchase = $model;
    }

    /**
     * @brief とらのこ会員に対する売上を起票する
     * @return void
     */
    private function generatePurchaseForCustomer($items)
    {
        $model = new PurchaseForm([
            'customer_id'=> $this->customer->customer_id,
            'shipped'    => (int) $this->shipped,
            'paid'       => (int) $this->paid,
            'payment_id' => $this->payment_id,

            'branch_id'  => $this->branch->branch_id,
            'company_id' => $this->branch->company_id,
            'items'      => $items,
        ]);
        $model->compute(false);

        if($model->postage)
        {
            $model->total_charge -= $model->postage;
            $model->postage       = 0;
        }
        if($this->paid)
            $model->receive = $model->total_charge;
        else
            $model->note = "当社にて入金が確認でき次第、会員期間を延長いたします";

        $this->_purchase = $model;
    }

    private function sendMail()
    {
        if(! $this->_purchase || $this->_purchase->isNewRecord)
            return $this->failure('paid',"Purchaseが未定義または保存に失敗しました");

        $mailer = new ToranokoApplicationMail([
            'branch'    => $this->branch,
            'customer'  => $this->customer,
            'agency'    => $this->seller,
            'product'   => $this->product,
            'invoiceC'  => $this->seller ? $this->_pointing : $this->_purchase,
            'invoiceA'  => $this->seller ? $this->_purchase : null,
            'paid'      => $this->paid,
        ]);
        return $mailer->sendMail();
    }

    public function validateMembercode($attr, $params)
    {
        if(Membercode::find()->where(['code'=>$this->membercode, 'customer_id'=>null])->exists())
            return true;

        if($this->customer)
            if(Membercode::find()->where([
                'code'       => $this->membercode,
                'customer_id'=> $this->customer->customer_id
            ])->exists())
                return true;

        $this->addError($attr, "会員証NOが検索できません。すでに使われているか、DBに未登録です");

        return false;
    }

    public function validateMembership($attr, $params)
    {
    }

    public function validateSeller($attr, $value)
    {
        $model = $this->$attr;

        if($model instanceof Customer && ($model->isHomoeopath() || $model->isAgency()))
            return true;

        $this->addError($attr, "sellerが不正です");

        return false;
    }

}
