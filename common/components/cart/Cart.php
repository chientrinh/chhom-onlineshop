<?php

namespace common\components\cart;

use Yii;
use \yii\helpers\ArrayHelper;
use \common\models\Payment;

/**
 * Abstract of Shopping Cart (accessible via frontend, backyard, console)
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/cart/Cart.php $
 * $Id: Cart.php 3009 2016-10-23 01:02:02Z mori $
 */

abstract class Cart extends \yii\base\Model
{
    public $company = null;
    public $branch  = null;
    public $customer;
    public $payments = [];
    public $recipes;

    protected $_branch;
    protected $_items;
    protected $_purchase;
    protected $_delivery;

    private $_ifinder;

    public function init()
    {
        parent::init();
        $this->_items    = [];
        $this->_purchase = new \common\models\PurchaseForm([
            'scenario' => \common\models\Purchase::SCENARIO_CREATE,
            'status'   => \common\models\PurchaseStatus::PKEY_INIT,
        ]);
        $this->_delivery = new Delivery();
        $this->_ifinder  = new ItemFinder();

        if(! $this->company)
             $this->company  = new \common\models\NullCompany();

        if(! $this->branch)
             $this->branch   = \common\models\Branch::findOne(0); // 仮想店舗

        if(! $this->customer)
             $this->customer = new \common\models\NullCustomer();

        if(! $this->recipes)
            $this->recipes = [];

        $this->initPayment();

    }

    /**
     * configure $this->payments and refresh $this->payment_id accordingly
     */
    abstract protected function initPayment();

    public function rules()
    {
        return [
            ['customer', 'customerValidation', 'skipOnEmpty'=> true ],
            ['delivery', 'validateTime'],
        ];
    }

    public function attributes()
    {
        return ['customer','items','purchase','delivery'];
    }

    public function add($product_id, $options=[])
    {
        if($options && ! is_array($options) && ! is_numeric($options))
            throw new \yii\base\UnknownPropertyException();

        if($options && isset($options['recipe_id']))
        {
            $recipe_id = (int) $options['recipe_id'];
//            unset($options['recipe_id']);
        }
        

        if(is_numeric($options)) // treat as qty is specified
        {
            $vol     = (int) $options;
            $options = ['qty' => $vol];
        }
        
        $qty = 1; // default quantity
        if($options && isset($options['qty']))
        {
            $qty = (int) $options['qty'];
            unset($options['qty']);
        }
        
        if(isset($recipe_id)) {
            $options['recipe_id'] = $recipe_id;
        }

        
        $item = $this->_ifinder->find($product_id, $options);
        if(! $item)
        {
            Yii::warning(sprintf('%s::%s() failed: No such product (id=%s)', $this->className(), __FUNCTION__, $product_id));
            return false;
        }
        $item->qty = $qty;

        if(isset($options['companion'])) {
            $_SESSION['companion_item'] = $product_id;
            $_SESSION['companion_price'] = $options['companion_price'];
            $_SESSION['companion_tax'] = $options['companion_tax'];
            $_SESSION['companion_subscription'] = $options['companion_subscription'];
            Yii::$app->session['companion_data'] = $options['companion_data'];
            Yii::$app->session['companion_info_id'] = $options['companion_info_id'];
            $item->setPrice($options['companion_price']);
            $item->setTax($options['companion_tax']);
            $item->setUnitPrice($options['companion_price']);
            $item->setUnitTax($options['companion_tax']);
        }            


        foreach($this->_items as $k => $i)
        {
            if(true === $item->compare($i))
            {
                $this->_items[$k]->increase($qty);
                $qty = 0; // added successfully
            }
        }

        if(0 < $qty) // not found in current items
        {
            $this->_items[] = $item;
        }


        return $item;
    }


    public function append($item)
    {
        $this->_items[] = $item;

        return true;
    }
    /**
     * @return false | CartItem
     */
    public function del($idx)
    {
        if((count($this->_items) <= $idx) || ($idx < 0))
            return false;
        
        $recipe_id = null;
        
        $item = $this->_items[$idx];
        if(isset($item->recipe_id))
            $recipe_id = $item->recipe_id;

        unset($this->_items[$idx]);
        $this->_items = array_values($this->_items); // refresh array keys

        if($recipe_id) {
            $recipe_ids =  ArrayHelper::getColumn($this->_items, 'recipe_id');
            $del = false;
            foreach($this->recipes as $key => $val) {
                if(!in_array($val, $recipe_ids)) {
                    unset($this->recipes[$key]);
                    break;
                } 
            }
            array_values($this->recipes);
        }

        /**
         * ライブ配信用処理
         * お弁当・ランチ予約等の抹消
         */
        if('echom-frontend' == Yii::$app->id) {
            $purchase = $this->purchase;
            $notes = isset($_SESSION['live_notes']) ? $_SESSION['live_notes'] : [];
            $product_id = $item->model->product_id;


            if(count($this->_items) == 0) {
                $notes = [];
            }

            foreach($this->_items as $k => $i)
            {
                if(in_array($product_id, [1765,1766,1767,1768,1769,1770,1771,1772,1775,1776,1777,1778,1908])) {
                    unset($notes[$product_id]);
                }
            }
            $_SESSION['live_notes'] = $notes;
            $note = implode(" ",array_values($notes));
            $purchase->note = $note;

        }
        //unset($_SESSION['live_notes']);
        return $item;
    }
    
    public function recipeDel($recipe_id)
    {
        foreach($this->recipes as $key => $val) {
            if($recipe_id == $val) {
                unset($this->recipes[$key]);
                break;
            } 
        }
        array_values($this->recipes);
        
        $items = $this->_items;
        $keys = array_keys($this->_items);
        foreach($keys as $key) {
            if($recipe_id == $items[$key]['recipe_id']) {
                unset($items[$key]);
            }
        }
        $this->_items = array_values($items);
        
        return $this->recipes;
        
    }

    public function updateCampaign($campaign)
    {
        $this->_purchase->campaign = $campaign;
        if(!$this->hasError())
            return true;
        return false;
    }

    public function updateAgent($direct_customer)
    {
        $this->purchase->agent_id = Yii::$app->user->id;
        $this->customer = $direct_customer;
        // 配送先を変更する
        $addr = \common\models\Membercode::findOne([
            //                    'customer_id' => Yii::$app->user->id,
                                'code'          => $direct_customer->code,
                            ]);
        $this->setDestination($addr->customer);
        $this->delivery->code = $addr->code;
        $this->purchase->setCustomer($direct_customer);
        // 支払い方法もサポート申込専用のものに切り替える
        $this->setPayment(Payment::PKEY_SUPPORT);     
        if(!$this->hasErrors())
            return true;
        return false;
    }

    public function unsetAgent()
    {
        if(!$this->purchase->agent_id)
            return false;

        $this->purchase->agent_id = null;
        $customer = Yii::$app->user->identity;
        // 元のCustomerに差し替える
        $this->setDestination($customer);
        $this->delivery->code = "";
        $this->purchase->setCustomer($customer);
        return true;
    }


    /**
     * パラメータからライブ配信の「お子様連れ」「ランチ予約」をNoteにセットする
     */
    private function getLiveNote($product_id, $options)
    {
        $product = \common\models\Product::findOne($product_id);
        $note = "";

        $pref = "";
        switch($product_id){
          case 1765:
              $pref = "東京";
              break;
          case 1766:
              $pref = "東京";
              break;
          case 1775:
              $pref = "東京";
              break;
          case 1767:
              $pref = "札幌";
              break;
          case 1768:
              $pref = "札幌";
              break;
          case 1776:
              $pref = "札幌";
              break;
          case 1769:
              $pref = "名古屋";
              break;
          case 1770:
              $pref = "名古屋";
              break;
          case 1777:
              $pref = "名古屋";
              break;
          case 1771:
              $pref = "大阪";
              break;
          case 1772:
              $pref = "大阪";
              break;
          case 1778:
              $pref = "大阪";
              break;
          default:

        }

        if(isset($options['children'])) {
            // $children = (int)($options['children']) == 0 ? '同伴なし' : (int)($options['children']).'名同伴';
            $children = (int)($options['children']);
            $note = $pref." 子連れ ".$children;
        }

        if(isset($options['lunchbox_200606'])) {
            // $lunchbox = (int)($options['lunchbox_200606']) == 0 ? '不要' : (int)($options['lunchbox_200606']).'個';
            $lunchbox = (int)($options['lunchbox_200606']);
            $note .= " 6/6お弁当 ".$lunchbox;
        }

        if(isset($options['lunchbox_200607'])) {
            // $lunchbox = (int)($options['lunchbox_200607']) == 0 ? '不要' : (int)($options['lunchbox_200607']).'個';
            $lunchbox = (int)($options['lunchbox_200607']);
            $note .= " 6/7お弁当 ".$lunchbox;
        }

        if(isset($options['lunch_200606'])) {
            // $lunch = (int)($options['lunch_200606']) == 0 ? '不要' : (int)($options['lunch_200606']).'個';
            $lunch = (int)($options['lunch_200606']);
            $note .= " 6/6ランチ ".$lunch;
        }

        if(isset($options['lunch_200607'])) {
            // $lunch = (int)($options['lunch_200607']) == 0 ? '不要' : (int)($options['lunch_200607']).'個';
            $lunch = (int)($options['lunch_200607']);
            $note .= " 6/7ランチ ".$lunch;
        }

        return $this->_purchase->note = $note; 
    }


    public function getChange()
    {
        return $this->_purchase->change;
    }

    public function getDelivery()
    {
        return $this->_delivery;
    }

    public function getDiscount()
    {
        return $this->_purchase->discount;
    }

    public function getItem($seq)
    {
        if(($seq < 0) || (count($this->items)-1 < $seq))
            return false;

        return $this->_items[$seq];
    }

    public function getItems()
    {
        return $this->_items;
    }

    public function getItemCount()
    {
        $rows = ArrayHelper::getColumn($this->_items, 'qty');
        
        return array_sum($rows);
    }

    public function getItemBasePrice()
    {
        $rows = ArrayHelper::getColumn($this->_items, 'basePrice');
        
        return array_sum($rows);
    }

    public function getHandling()
    {
        return $this->_purchase->handling;
    }

    public function getName()
    {
        $name = $this->company->name ? $this->company->name . 'カート' : "２店舗一括発送カート";
        if ($this->company->company_id === \common\models\Company::PKEY_TY)
            $name = '日本豊受自然農カート';

        if (Yii::$app->id === 'app-hpfront')
            $name = 'ホメオパシー出版カート';

        return $name;
    }

    public function getPayment()
    {
        return $this->_purchase->payment;
    }

    public function getPointGiven()
    {
        return $this->_purchase->point_given;
    }

    public function getPointConsume()
    {
        return $this->_purchase->point_consume;
    }

    public function getPostage()
    {
        return $this->_purchase->postage;
    }

    public function getPurchase()
    {
        return $this->_purchase;
    }

    public function getReceive()
    {
        return $this->_purchase->receive;
    }

    public function getSubtotal()
    {
        return $this->_purchase->subtotal;
    }

    public function getTax()
    {
        return $this->_purchase->tax;
    }

    public function getTaxable()
    {
        return ($this->subtotal - $this->pointConsume);
    }

    public function getTotalCharge()
    {
        return $this->_purchase->total_charge;
    }

    /**
     * 品目に酒類を含むかどうか
     * @return bool
     */
    public function hasLiquor()
    {
        foreach($this->items as $item)
            if($item->isLiquor()){ return true; }

        return false;
    }

    /* @return bool */
    public function setBranch($model)
    {
        if($model->className() != \common\models\Branch::className())
            return false;

        $this->_branch = $model;
        return true;
    }

    /* @return bool */
    public function setCustomer($customer)
    {
        if(is_numeric($customer))
            if(null === ($customer = \common\models\Customer::findOne(['customer_id' => $customer])))
               $customer = new \common\models\NullCustomer();

        $this->customer = $customer;
        $this->_purchase->customer_id = $customer->customer_id;

        if(null === $this->_delivery->name01) // _delivery not set
            $this->setDestination($customer);

        return true;
    }

    /**
     * @return bool
     */
    public function setDelivDate($str)
    {
        return $this->_delivery->setDate($str);
    }

    /**
     * @return bool
     */
    public function setDelivTime($id)
    {
        return $this->_delivery->setTime($id);
    }

    /**
     * @return bool
     */
    public function setDestination($addrbook)
    {
        return $this->_delivery->setDestination($addrbook);
    }

    /**
     * @return bool
     */
    public function setDiscount($amount)
    {
        $prev = $this->_purchase->discount;

        $this->_purchase->discount = $amount;
        if(! $this->_purchase->validate(['discount'])) // error
        {
            $this->_purchase->revertAmount('discount', $prev);
            return false;
        }

        return true;
    }
    
    /**
     * @return true
     */
    public function setMsg($msg)
    {
        $this->_purchase->customer_msg = \yii\helpers\Html::encode($msg);
        return true;
    }

    /**
     * @return true
     */
    public function setNote($text)
    {
        $this->_purchase->note = \yii\helpers\Html::encode($text);
        return true;
    }

    /**
     * @return bool
     */
    public function setPointConsume($amount)
    {
        $prev = $this->_purchase->point_consume;

        $this->_purchase->point_consume = $amount;

        if(! $this->_purchase->validate(['point_consume'])) // error
        {
            $this->_purchase->revertAmount('point_consume', $prev);
            return false;
        }

        return ($amount == $this->_purchase->point_consume);
    }

    public function setPayment($pk)
    {
        $this->_purchase->payment_id = $pk;
    }

    public function setPointGiven($amount)
    {
        $this->_purchase->point_given = $amount;
    }

    protected function setSubtotal($amount)
    {
        $this->_purchase->subtotal = $amount;
    }

    protected function setTax($amount)
    {
        $this->_purchase->tax = $amount;
    }

    protected function setTotalCharge($amount)
    {
        $this->_purchase->total_charge = $amount;
    }

    private function compute()
    {
        $this->_purchase->items       = $this->_items;
        $this->_purchase->customer_id = $this->customer->id;
        $this->_purchase->company_id  = $this->company->company_id;
        $this->_purchase->branch_id   = $this->branch->branch_id;
        $this->_purchase->delivery    = $this->_delivery;
        $this->_purchase->compute();

        $this->_items = $this->_purchase->items;

        return;
    }

    protected function computePointGiven()
    {
        $pt = 0;
        foreach($this->_items as $item)
        {
            $pt += $item->pointTotal;
        }
        $this->pointGiven = $pt;
    }

    public function dump()
    {
        $buffer = [
            'items' => [],
            'purchase' => $this->_purchase->attributes,
            'delivery' => $this->_delivery->attributes,
            'recipes'  => $this->recipes,
        ];

        foreach($this->_items as $item)
        {
            if(is_a($item, ComplexRemedyForm::className()               ) ||
               is_a($item, \common\models\webdb\RemedyStock::className())
            )
                $buffer['items'][] = ['class'   => $item->className(),
                                      'attr'    => $item->dump(),
                                      'scenario'=> $item->scenario ];
            else
                $buffer['items'][] = $item->attributes;
        }

        // clear null params
        foreach(array_keys($buffer) as $key)
        {
            foreach($buffer[$key] as $k => $v)
                if(null === $v)
                    unset($buffer[$key][$k]);
        }

        return $buffer;
    }

    public function feed($buffer)
    {
        $ret = false;
        if(isset($buffer['items']))
            $ret = $this->feedItems($buffer['items']);

        if(isset($buffer['purchase']))
            $this->_purchase = new \common\models\PurchaseForm($buffer['purchase']);

        if($payment_id = $this->_purchase->payment_id)
            if(! in_array($payment_id, $this->payments))
                $this->_purchase->payment_id = array_shift(array_values($this->payments));

        if(isset($buffer['delivery']))
        {
            $params = [
                $this->_delivery->formName() => $buffer['delivery']
            ];

            // 直送先の会員証NOがセットされていたら、サポート注文として処理する（伝票の顧客IDを直送先に）
            if(isset($buffer['delivery']['code']) && ($code = $buffer['delivery']['code']) != false) {
                $direct_customer = \common\models\Customer::findByBarcode($buffer['delivery']['code']);
                if($direct_customer)
                    $this->_purchase->customer_id = $direct_customer->customer_id;
            }        
            $ret = $this->_delivery->load($params) || $ret;
        }

        if(isset($buffer['recipes']))
            $this->recipes = $buffer['recipes'];
        return $ret;
    }

    protected function feedItems($rows)
    {
        if(! is_array($rows))
            return false;

        $this->_items = [];

        foreach($rows as $item)
        {
            if(array_key_exists('class', $item))
            {
                $model = new $item['class']();
                $attr  = $item['attr'];
                if(isset($item['scenario']))
                    $model->scenario = $item['scenario'];

                if(method_exists($model, 'feed'))
                    $model->feed($attr);
                else
                    foreach($attr as $name => $value)
                        if($model->canSetProperty($name)){ $model->$name = $value; }

                $this->_items[] = $model;
                continue;
            }

            if(! array_key_exists('id', $item))
            {
                Yii::error(sprintf('%s %s() failed, no id specified in array',$this->className(),__FUNCTION__));
                continue;
            }
            $product_id = $item['id'];
            $options    = $item;
            unset($options['id']);

            if(! $this->add($product_id, $options))
                Yii::error('%s %s() failed, no id specified in array',$this->className(),__FUNCTION__);
        }
    }

    public function isGift()
    {
        return $this->_delivery->gift;
    }

    private function insertRecords()
    {
        if(! $this->_purchase->isNewRecord)
        {
            Yii::warning(['trying insert a existing record',$this->_purchase->attributes]);
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        $this->_purchase->customer_id = $this->customer->id;
        $this->_purchase->company_id  = $this->company->company_id;
        $this->_purchase->branch_id   = $this->_branch ? $this->_branch->branch_id : 0;
        if(! $this->customer->id)
            $this->_purchase->email   = $this->customer->email;

        // サポート申込が設定されていたら、支払い方法を「指定なし」に強制変更する
        if($this->_purchase->agent_id) {
            $agent = \common\models\Customer::findOne(['customer_id' => $this->_purchase->agent_id]);
//            $this->_purchase->note .= "※".$agent->name."様より「サポート注文」指定で注文を承りました";
            $this->_purchase->payment_id = \common\models\Payment::PKEY_SUPPORT;
        }
        
        try
        {
            //detect company_id of purchase
            $companies = $this->_purchase->companies;
            if($companies && (1 == count($companies)))
            {
                $company = array_shift($companies);
                $this->_purchase->company_id = $company->company_id;
            }

            $this->_purchase->delivery = $this->_delivery->getModel();

            if(! $this->_purchase->save()) {
                throw new \yii\db\Exception('purchase->save() failed');
            }

            if(! empty($this->recipes))
                foreach($this->recipes as $recipe_id)
                {
                    if($recipe = \common\models\Recipe::findOne($recipe_id))
                    {
                        if($recipe->sold())
                        {
                            $ltb = new \common\models\LtbPurchaseRecipe([
                                'purchase_id' => $this->_purchase->purchase_id,
                                'recipe_id'   => $recipe->recipe_id,
                            ]);
                            $ltb->save();
                        }
                        else
                            Yii::error(['update Recipe status failed',
                                        'attributes' => $recipe->attributes,
                                        'errors'     => $recipe->errors]);
                    }
                }
        }
        catch (\yii\db\Exception $e)
        {
            Yii::warning($e->__toString(), $this->className().'::'.__FUNCTION__);

            $transaction->rollBack();
            return false;
        }

        $transaction->commit();
        return true;
    }

    public function save()
    {

        if(! $this->validate())
            return false;

        return $this->insertRecords();
    }

    public function updateQty($idx, $qty)
    {
        if(count($this->_items) <= $idx)
            false;

        $item = $this->_items[$idx];

        if(! $qty)
            return $this->del($idx);

        $this->_items[$idx]->qty = $qty;

        return $this->_items[$idx];
    }

    public function updateGift($bool)
    {

        $pk = Payment::find()->where(['payment_id' => $this->purchase->payment_id])

                             ->andWhere(['payment_id' => $this->payments])
                             ->select('payment_id')
                             ->scalar();
        if(!$pk)
            return false;

        $this->setPayment($pk);
        $this->_delivery->gift = (int)$bool;
        return true;
    }

    public function checkForGift($cart_idx)
    {
        return (! in_array($cart_idx, [\common\models\Company::PKEY_TROSE,]) && ($user = Yii::$app->user->identity) && (
                            $user->isAgency() || 
                            ((\common\models\CustomerGrade::PKEY_KA <= $user->grade_id) && isset($user->ysdAccount) && $user->ysdAccount->isValid())
                        )
                        && ($p = $this->payment) && in_array($p->payment_id,[
                               Payment::PKEY_BANK_TRANSFER,
                               Payment::PKEY_DIRECT_DEBIT,
                               Payment::PKEY_DROP_SHIPPING,
                            ])
                    );

    }

    public function getGiftName()
    {
        return ($this->gift == 1) ? '非表示' : '表示';
    }

    public function beforeValidate()
    {
        $this->compute();

        return parent::beforeValidate();
    }

    public function customerValidation($attr, $params)
    {
        if(! $this->customer->isNewRecord)
            return true;

        return $this->customer->validate('email');
    }

    public function validateTime($attr, $param)
    {
        $model = new \common\models\DeliveryDateTimeForm([
            'company_id' => ($c = $this->company) ? $c->company_id : null
        ]);
        $params = [
            $model->formName() => $this->delivery->attributes
        ];
        $model->load($params);

        if(! $model->zip01 && ! $model->zip02 && ! $model->pref_id)
            return false; // validation failure, but suppress errors

        if(! $model->validate())
            $this->addError($attr, array_shift(array_values($model->firstErrors)));

        return $this->hasErrors($attr);
    }

    public function validate($attr=null, $clearErrors=true)
    {
        $ret = parent::validate($attr, $clearErrors);

        if(! $this->_delivery->validate())
            $ret = false;

        if($this->customer->customer_id != 15735) {
            if(! $this->_purchase->validate())
                 $ret = false;
        } else {
            if(! $this->_purchase->validate(['point_consume']))
                 $ret = false;
        }

        if($this->customer->hasErrors('email'))
            $this->addError('customer',"お客様の設定 または ログイン が完了していません");

        elseif($this->_purchase->hasErrors())
            $this->addErrors(['purchase'=> implode('',array_values($this->_purchase->firsterrors))]);

        elseif($this->_delivery->hasErrors())
            'echom-frontend' != Yii::$app->id ? $this->addError('delivery',"お届け先の入力が完了していません") :  $this->addError('delivery',"ご連絡先の入力が完了していません");

        else
        foreach($this->_items as $item)
            if(! $item->validate())
        {
            $ret = false;

            if($item->hasErrors())
                foreach($item->errors as $k => $v)
                    $this->addError('items',serialize($v));
        }


        return $ret;
    }

    public function afterValidate()
    {
        parent::afterValidate();
    }

}

