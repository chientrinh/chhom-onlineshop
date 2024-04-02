<?php

namespace common\components\cart;

use Yii;
use \yii\helpers\ArrayHelper;
use \common\models\Branch;
use \common\models\Company;
use \common\models\Product;
use \common\models\Payment;
use \common\models\LiveInfo;
use \common\models\LiveItemInfo;
use \common\models\Streaming;

/**
 * Shopping Cart Manager (possibly accessible via 一般かご@frontend only)
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/cart/CartManager.php $
 * $Id: CartManager.php 4156 2019-04-14 16:40:31Z mori $
 */

class CartManager extends \yii\base\Model
{
    private $_carts;
    private $_user;
    const DEFAULT_CART_IDX = 0;

    public function init()
    {
        parent::init();

        if(Yii::$app instanceof \yii\web\Application &&
         ! Yii::$app->user->isGuest &&
           Yii::$app->user->identity instanceof \common\models\Customer)
        {
            $this->_user = Yii::$app->user->identity;
        }
        else
            $this->_user = new \common\models\NullCustomer();

        $this->_carts = [];
        $this->_carts[self::DEFAULT_CART_IDX] = new MixedCart(['customer'=>$this->_user]);
        $this->_carts[Company::PKEY_TY]       = new TyCart(   ['customer'=>$this->_user]);
        $this->_carts[Company::PKEY_TROSE]    = new TroseCart(['customer'=>$this->_user]);
    }

    public function addProduct($pid, $options)
    {
        $product = Product::findOne($pid);
        if(! $product || ! $product->company)
            return false;

        /**
         * 豊受自然農の無添加おせち　のみの処理。豊受カートにおいて、「おせち」は他の商品と同時にカートに入れることは出来ないようにする 2017/12/22
         **/
        if($product->company->company_id == Company::PKEY_TY) {
            $items = $this->_carts[Company::PKEY_TY]->items;
            $arr = array();
            if(0 < count($items)) {
                foreach ($items as $item)
                    $arr[] = $item->getModel()->product_id;

                if((in_array(Product::PKEY_OSECHI, $arr) && $pid != Product::PKEY_OSECHI) || !in_array(Product::PKEY_OSECHI, $arr) && $pid == Product::PKEY_OSECHI) {
                    return false;
                }
            }
        }

        // ライブ配信専用処理
        // アイテムを追加するたびに備考をリセットする
        if('echom-frontend' == Yii::$app->id) {
            $cart  = $this->_carts[self::DEFAULT_CART_IDX];
            $items = $cart->items;
            $companion_item = Yii::$app->session->get('companion_item');
            $liveItemInfo1 = LiveItemInfo::find()->where(['product_id'=>$pid])->one();
            $liveInfo1 = $liveItemInfo1 ? $liveItemInfo1->info : null;
            $support_entry = false;
            if(isset($liveInfo1) && $liveInfo1->support_entry) {
                $support_entry = true;
            }

            if(0 < count($items)) {
                foreach ($items as $item) {
                    $product_id = $item->getModel()->product_id;
                    if($pid != $product_id) {
                        $liveItemInfo = LiveItemInfo::find()->where(['product_id'=>$product_id])->one();
                        $liveInfo = $liveItemInfo ? $liveItemInfo->info : null;
                        // カートにサポート申込対応チケットが入っている
                        if(isset($liveInfo) && $liveInfo->support_entry) {
                            if(!$support_entry) {
                                Yii::$app->session->addFlash('error', "コングレスの申込は、他のチケットと共にカートに入れることはできません。別途お申込みください。");
                                return false;    
                            }
                        } else {
                            if($support_entry) {
                                Yii::$app->session->addFlash('error', "コングレスの申込は、他のチケットと共にカートに入れることはできません。別途お申込みください。");
                                return false;
                            }
                        }
                    }

                    if(isset($options['companion_item'])) {
                    // 有料イベント以外が入っている場合
                        if($pid === $options['companion_item'] && $product_id != $pid) {
                            Yii::$app->session->addFlash('error', "有料イベントは他の商品と共にカートに入れることはできません");
                            return false;
                        }
                    }
                    // すでに有料チケットが入っている場合
                    if($companion_item && $companion_item == $product_id) {
                        if($pid == $product_id) {
                            Yii::$app->session->addFlash('error', "有料イベントは１つだけカートに入れることができます");
                            return false;
                        } else {
                            Yii::$app->session->addFlash('error', "有料イベントと共にカートに入れることはできません");
                            return false;
                        }

                    }
                }
            }

            if(isset($options['companion'])) {
                $liveInfo = LiveInfo::findOne($options['info_id']);
                if(isset($options['companion_subscription']))
                    $capacity = $liveInfo->capacity;
                    $left = $capacity - $liveInfo->subscription;
                    if($options['companion_subscription'] > $left ) {
                        Yii::$app->getSession()->addFlash('error',"申込人数が定員をオーバーしています");
                        return false;
                    }


                $items = $cart->items;
                if(0 < count($items)) {
                    foreach ($items as $item) {
                        if($pid == $options['companion_item'] && $pid == $item->getModel()->product_id) {
                            Yii::$app->session->addFlash('error', "有料イベントは１つだけカートに入れることができます");
                            return false;
                        }
                    }
                }
            }


            $purchase = $cart->purchase;
            $notes = isset($_SESSION['live_notes']) ? $_SESSION['live_notes'] : [];

            if(isset($options['info_id'])) {
                $notes[$pid] = $this->getLiveNote($pid, $options);
                $this->setLiveOptions($purchase,$options);
                $_SESSION['live_notes'] = $notes;
                $note = implode(" ",array_values($notes));
                $purchase->note = $note;

                if(in_array($product->company->company_id,[Company::PKEY_TY, Company::PKEY_TROSE]))
                    return $this->_carts[$product->company->company_id]->add($product->product_id, $options);
                else
                    return $this->_carts[self::DEFAULT_CART_IDX]->add($product->product_id, $options);
            }

            if(count($purchase->items) == 0) {
//                if(in_array($pid, [1765,1766,1767,1768,1769,1770,1771,1772,1775,1776,1777,1778,1922,1954,1955,1966,1967,2291])) {
                if(in_array($pid, [1765,1766,1767,1768,1769,1770,1771,1772,1775,1776,1777,1778,1922,1954,1955,1966,1967,2291,2342,2343,2344,2345,2346,2347,2348])) {
                    $notes[$pid] = $this->getLiveNote($pid, $options);
                }
            }

            foreach($purchase->items as $k => $i)
            {
//                if(in_array($pid, [1765,1766,1767,1768,1769,1770,1771,1772,1775,1776,1777,1778,1922,1954,1955,1966,1967,2291])) {
                if(in_array($pid, [1765,1766,1767,1768,1769,1770,1771,1772,1775,1776,1777,1778,1922,1954,1955,1966,1967,2291,2342,2343,2344,2345,2346,2347,2348])) {
                    $notes[$pid] = $this->getLiveNote($pid, $options);
                }
            }
            $_SESSION['live_notes'] = $notes;
            $note = implode(" ",array_values($notes));
            $purchase->note = $note;
        }

        if(in_array($product->company->company_id,[Company::PKEY_TY, Company::PKEY_TROSE]))
            return $this->_carts[$product->company->company_id]->add($product->product_id, $options);
        else
            return $this->_carts[self::DEFAULT_CART_IDX]->add($product->product_id, $options);
    }

    /**
     * setLiveOptions($purchase,$options)
     */
    private function setLiveOptions($purchase,$options)
    {
        $cart  = $this->_carts[self::DEFAULT_CART_IDX];
        $purchase = $cart->purchase;
        $liveInfo = LiveInfo::findOne($options['info_id']);
        if($liveInfo) {

            if(isset($options['coupon']) && "" != $options['coupon']) {
                // クーポン適用
                $purchase->discount += $liveInfo->coupon_discount;
            }

        }        
    }



    /**
     * パラメータからライブ配信の「お子様連れ」「ランチ予約」をNoteにセットする
     */
    public function getLiveNote($product_id, $options)
    {
        $product = \common\models\Product::findOne($product_id);
        $note = "";
        $cart  = $this->_carts[self::DEFAULT_CART_IDX];
        $purchase = $cart->purchase;

        $info_id = isset($options['info_id']) ? $options['info_id'] : (isset($options['companion_info_id']) ? $options['companion_info_id'] : null);

        if($info_id) {
            $liveInfo = LiveInfo::findOne($info_id);
            if($liveInfo) {
                # TODO: ライブ配信と商品（チケット）が一対一である前提
                $streaming = Streaming::find()->where(['product_id' => $product_id])->all();
                $date = "";
                # チケット（商品）の商品名にある日付と配信開始日時を比較して一致すればOK、指定フォーマットをチケット詳細へ
                if(count($streaming) > 0)
                    $date = $this->ticketDateCheck($streaming[0]->expire_from, $product->name);

                if($date != "") {
                    $note .= $date."・";
                }

                $note .= $product_id;
                if(isset($options['school'])) {
                    $school = $options['school'];
                    if($school == "99")
                        $school = "自宅受講";
                        $note .= "・".$school;
                }

                if(isset($options['option'])) {
                    if($options['option'] == "1") {
                        // 有料オプション適用
                        $note .= "・".$liveInfo->option_name;
                    }
                }

                // セッションに同行者フラグを立てる
                if(isset($options['companion'])) {
                    $_SESSION['live_notes_companion'] = 1;
                    $note .= "\n".$options['companion'];
                }

            
                if(isset($options['coupon']) && "" != $options['coupon']) {
                    $note .= "・".$liveInfo->coupon_name.':'.$options['coupon'];
                }
            }
            return $note;
        }

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

        if(isset($options['school'])) {
            $school = ($options['school']);
            $note = " ".$school;
        }
        if(isset($options['school1003'])) {
            $school1003 = ($options['school1003']);
            $note = " ".$school1003;
        }
        if(isset($options['school1004'])) {
            $school1004 = ($options['school1004']);
            $note = " ".$school1004;
        }
        if(isset($options['school1'])) {
            $school1 = ($options['school1']);
            $note = " ".$school1;
        }
        if(isset($options['school2'])) {
            $school2 = ($options['school2']);
            $note = " ".$school2;
        }

        if(isset($options['children'])) {
            // $children = (int)($options['children']) == 0 ? '同伴なし' : (int)($options['children']).'名同伴';
            $children = (int)($options['children']);
            $note .= $pref." 子連れ ".$children;
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
        if(isset($options['lunch_201003'])) {
            // $lunch = (int)($options['lunch_201003']) == 0 ? '不要' : (int)($options['lunch_201003']).'個';
            $lunch = (int)($options['lunch_201003']);
            $note .= " お弁当・ランチ ".$lunch;
        }

        if(isset($options['lunch_201004'])) {
            // $lunch = (int)($options['lunch_201004']) == 0 ? '不要' : (int)($options['lunch_201004']).'個';
            $lunch = (int)($options['lunch_201004']);
            $note .= " お弁当・ランチ ".$lunch;
        }

        if(isset($options['lunch_210227'])) {
            // $lunch = (int)($options['lunch_210227']) == 0 ? '不要' : (int)($options['lunch_210227']).'個';
            $lunch = (int)($options['lunch_210227']);
            $note .= " お弁当・ランチ ".$lunch;
        }

//2293
        if(isset($options['lunch_210417'])) {
            // $lunch = (int)($options['lunch_210417']) == 0 ? '不要' : (int)($options['lunch_210417']).'個';
            $lunch = (int)($options['lunch_210417']);
            $note .= " お弁当・ランチ ".$lunch;
        }


        return $note; 
    }


    public function addRemedy($rid, $pid, $vid, $qty=1, $prange=null, $recipe_id=null)
    {
        $param = ['potency_id' => $pid, 'vial_id' => $vid, 'qty' => $qty, 'prange_id' => $prange, 'recipe_id' => $recipe_id];
        return $this->_carts[self::DEFAULT_CART_IDX]->add($rid, $param);
    }

    public function appendCartItem($model)
    {
        $company_id = ArrayHelper::getValue($model, 'company.company_id', null);

        if(in_array($company_id, [Company::PKEY_TY, Company::PKEY_TROSE]))
            return $this->_carts[$company_id]->append($model);

        return $this->_carts[self::DEFAULT_CART_IDX]->append($model);
    }

    /**
     * 適用書を代理購入（サポート注文）
     *
     **/
    public function createRecipeCart($id)
    {
/*
        $this->_carts[Company::PKEY_HJ] = new RecipeCart(['recipes'=>[$id]]);
*/
        if(! $model = \common\models\Recipe::findOne($id))
            throw new \yii\base\UserException("No such recipe_id: $id");

        $cart = $this->getCart($this::DEFAULT_CART_IDX);
        if(in_array($model->recipe_id, $cart->recipes))
        {
            Yii::$app->session->addFlash('error', "適用書 {$model->recipe_id} は追加済みです");
            return false;
        }
        $this->setRecipe($id);
        $items = $model->parentItems;
        foreach($items as $item)
        {
            if(0 < $item->product_id) {
                $this->addProduct($item->product_id, ['qty' => $item->quantity, 'name'=>$item->name, 'recipe_id' => $id]);
                continue;
            }
            if(! $item->children) {
                $this->addRemedy($item->remedy_id, $item->potency_id, $item->vial_id, $item->quantity, null, $id);
                continue;
            }
            $complex_item = \common\components\cart\ComplexRemedyForm::convertFromRecipeItem($item);
            if(is_array($complex_item)) {
                foreach($complex_item as $item) {
                    $item->recipe_id = $id;
                    $this->appendCartItem($item);
                }
            }
            elseif($complex_item) {
                $complex_item->recipe_id = $id;
                $this->appendCartItem($complex_item);
            }

            if(! $complex_item)
            {
                Yii::error(['failed conversion',
                            'recipe_id'  => $item->recipe_id,
                            'seq'        => $item->seq,
                            'name'       => $item->name,
                ],self::className().'::'.__FUNCTION__);
                return false;
            }
        }


        if(($client = $model->client) && ($parent = $client->parent)){ // クライアントは家族会員である（会員本人ではない）
            foreach(['zip01','zip02','pref_id','addr01','addr02','tel01','tel02','tel03','email'] as $key) {
                if(! $client->$key){ $client->$key = $parent->$key; } // 住所,TEL,emailを代入
            }
        }
        if($cart->updateAgent($client)) {
            $cart->customer = $client;
            $cart->delivery->code = $client->code;
            $cart->purchase->setCustomer($client);
            $prev_id = Yii::$app->session->id;


            if((\common\models\CustomerGrade::PKEY_AA <= $client->grade_id) && isset($client->ysdAccount->detail) && Yii::$app->id !== 'app-hpfront')
            {
                $cart->setPayment(\common\models\Payment::PKEY_DIRECT_DEBIT);
            }
            elseif($client->isAgency())
            {
                $cart->setPayment(\common\models\Payment::PKEY_BANK_TRANSFER);
            }

            $row = \common\models\WtbPurchase::findOne(Yii::$app->session->id);
            if(! $row)
            {
                $row = new \common\models\WtbPurchase();
                $row->session = Yii::$app->session->id;
            }
            $row->data = json_encode($cart->dump());

            // set a cookie to fetch current itemCount
            Yii::$app->response->cookies->add(new \yii\web\Cookie([
                'name'  => 'cartItemCount',
                'value' => $cart->itemCount,
            ]));

            $row->save();
        }

    }

    public function del($cart_idx, $item_idx)
    {
        if(! isset($this->_carts[$cart_idx]))
            return false;

        return $this->_carts[$cart_idx]->del($item_idx);
    }

    public function recipeDel($cart_idx, $recipe_id)
    {
        if(! isset($this->_carts[$cart_idx]))
            return false;

        return $this->_carts[$cart_idx]->recipeDel($recipe_id);
    }

    public function dump()
    {
        $buffer = [];

        foreach($this->_carts as $k => $cart)
        {
            if(! $cart->purchase->isNewRecord) // omit dump() if already inserted
                continue;

            $buffer[$k] = $cart->dump();
        }

        return $buffer;
    }

    public function getCart($idx)
    {
        if(! isset($this->_carts[$idx]))
            return null;

        return $this->_carts[$idx];
    }

    public function getCarts()
    {
        return $this->_carts;
    }

    public function getActiveCarts()
    {
        $carts = [];
        foreach($this->_carts as $k => $cart)
            if($cart->itemCount) // skip for empty carts
                $carts[$k] = $cart;

        return $carts;
    }

    public function getItemCount()
    {
        $count = 0;
        foreach($this->_carts as $cart)
        {
            if(! $cart->purchase->isNewRecord) // omit count if already inserted into dtb_purchase
                continue;

            $count += $cart->itemCount;
        }

        return $count;
    }

    public function setCustomer(\common\models\Customer $customer)
    {
        foreach(array_keys($this->_carts) as $idx)
            $this->_carts[$idx]->setCustomer($customer);
    }

    public function setDestination($cart_idx, $model)
    {
        if(! isset($this->_carts[$cart_idx]))
            return false;

        return $this->_carts[$cart_idx]->setDestination($model);
    }

    public function setRecipe($recipe_id, $proxy=false)
    {
        if($proxy) // Hpath が 自分のクライアントに成り代わって注文する
            $this->_carts[Company::PKEY_HJ] = new RecipeCart(['recipes'=>[$recipe_id]]);
        else
            $this->_carts[self::DEFAULT_CART_IDX]->recipes[] = $recipe_id;
    }

    public function updatePayment($cart_idx, $payment_id)
    {
        if(! isset($this->_carts[$cart_idx]))
            return false;

        $this->_carts[$cart_idx]->setPayment($payment_id);

        return true;
    }

    public function updateQty($cart_idx, $item_idx, $qty)
    {
        if(! isset($this->_carts[$cart_idx]))
            return false;

        return $this->_carts[$cart_idx]->updateQty($item_idx, $qty);
    }

    public function updateAddr($idx, $model)
    {
        if(! isset($this->_carts[$idx]))
            return false;

        return $this->_carts[$idx]->setDestination($model);
    }

    public function updateDateTime($idx, $date, $time_id)
    {
        if(! isset($this->_carts[$idx]))
            return false;

        $this->_carts[$idx]->delivery->date    = $date;
        $this->_carts[$idx]->delivery->time_id = $time_id;

        $model = $this->_carts[$idx]->delivery;
        return $model->validate('date') && $model->validate('time_id');
    }

    public function updateGift($idx, $bool)
    {
        return $this->_carts[$idx]->updateGift($bool);
    }

    public function updateMsg($idx, $msg)
    {
        if(! isset($this->_carts[$idx]))
            return false;

        return $this->_carts[$idx]->setMsg($msg);
    }

    public function updateNote($idx, $text)
    {
        if(! isset($this->_carts[$idx]))
            return false;

        return $this->_carts[$idx]->setNote($text);
    }

    public function updatePointConsume($idx, $pt)
    {
        if(! isset($this->_carts[$idx]))
            return false;

        return $this->_carts[$idx]->setPointConsume($pt);
    }


    /**
     * ポイントキャンペーンを適用する
     *
     **/
    public function updateCampaign($idx, $code)
    {

        if(! isset($this->_carts[$idx]))
            return false;

        // 有効なキャンペーンからのみ検索
        $campaign = \common\models\Campaign::find()->active()->andWhere(['and',['campaign_code' => $code, 'branch_id' => Branch::PKEY_FRONT]])->one();

        if(!isset($campaign))
            return false;


        // idxを元に指定したカートに引き渡す
        return $this->_carts[$idx]->updateCampaign($campaign);
    }

    /**
     * ポイントキャンペーンを解除する
     *
     **/
    public function unsetCampaign($idx)
    {

        if(! isset($this->_carts[$idx]))
            return false;


        // idxを元に指定したカートに引き渡す
        return $this->_carts[$idx]->unsetCampaign();
    }


    /**
     * キャンペーンコードから全カートにポイントキャンペーンを一括適用させる
     *
     **/
    public function setCampaign($code)
    {
        // キャンペーンコードから全カートに一括適用させる
        if(!isset($code))
            return false;

        $campaign = \common\models\Campaign::find()->where(['and',['campaign_code' => $code, 'branch_id' => Branch::PKEY_FRONT]])->one();
        if(!isset($campaign))
            return false;

        foreach(array_keys($this->_carts) as $idx)
            $this->_carts[$idx]->setCampaign($campaign);
    }


    /**
     * サポート注文を適用する
     *
     **/
    public function updateAgent($idx, $code)
    {

        if(! isset($this->_carts[$idx]))
            return false;

        $direct_customer = \common\models\Customer::findByBarcode($code);

        if(!isset($direct_customer))
            return false;


        // idxを元に指定したカートに引き渡す
        return $this->_carts[$idx]->updateAgent($direct_customer);
    }

    /**
     * サポート注文を解除する
     *
     **/
    public function unsetAgent($idx)
    {

        if(! isset($this->_carts[$idx]))
            return false;

        // idxを元に指定したカートに引き渡す
        return $this->_carts[$idx]->unsetAgent();
    }


    public function feed($buffer)
    {
        foreach($buffer as $idx => $data)
        {
/*
            if(Company::PKEY_HJ == $idx)
                $this->_carts[$idx] = new RecipeCart([
                    'recipes' => ArrayHelper::getValue($data, 'recipes', [])
                ]);
*/
            if(isset($this->_carts[$idx]))
                $this->_carts[$idx]->feed($data);
        }

        return true;
    }

    public function getErrors($attr = null)
    {
        $this->clearErrors();

        $err = [];
        foreach($this->_carts as $idx => $cart)
        {
            if($cart->hasErrors())
                $this->addErrors([$idx => $cart->errors]);
        }

        return parent::getErrors($attr);
    }

    /* @return Cart object or false */
    public function save($idx)
    {
        if(! array_key_exists($idx, $this->_carts))
            return false;

        $cart = $this->_carts[$idx];

        if(! $cart->validate())
            return false;

        if(! $cart->save())
        {
            Yii::error($cart->errors);
            return false;
        }

        return $cart->purchase->purchase_id;
    }

    /*
     * @return true: no error
     * @return false: validation() failed somewhere
     */
    public function validate($attr=null, $clearErrors=true)
    {
        $ret = parent::validate($attr, $clearErrors);

        foreach($this->_carts as $k => $cart)
        {
            if(false == $this->_carts[$k]->validate($attr, $clearErrors))
                $ret = false;
        }

        return $ret;
    }


    /**
     * チケット（商品）の商品名にある日付と配信開始日時を比較、日付を返す
     * @return string: n/j（D）
     * @param string yyyy-mm-dd, $product->name
     */
    public function ticketDateCheck($expire_from=null, $ticket_name)
    {
        $ret = "";

        if(!$expire_from)
            return $ret;

        $date1 = Yii::$app->formatter->asDate($expire_from, 'php:n/j（D）');
        $date2 = Yii::$app->formatter->asDate($expire_from, 'php:n/j(D)');
        $date3 = Yii::$app->formatter->asDate($expire_from, 'php:m/d（D）');
        $date4 = Yii::$app->formatter->asDate($expire_from, 'php:m/d(D)');
        $date5 = Yii::$app->formatter->asDate($expire_from, 'php:n/j（祝D）');
        $date6 = Yii::$app->formatter->asDate($expire_from, 'php:n/j(祝D)');
        $date7 = Yii::$app->formatter->asDate($expire_from, 'php:m/d（祝D）');
        $date8 = Yii::$app->formatter->asDate($expire_from, 'php:m/d(祝D)');
        $date9 = Yii::$app->formatter->asDate($expire_from, 'php:n/j（D祝）');
        $date10 = Yii::$app->formatter->asDate($expire_from, 'php:n/j(D祝)');
        $date11 = Yii::$app->formatter->asDate($expire_from, 'php:m/d（D祝）');
        $date12 = Yii::$app->formatter->asDate($expire_from, 'php:m/d(D祝)');


        if(strpos($ticket_name, $date1) !== false) {
            $ret = Yii::$app->formatter->asDate(mb_substr($date1,0,mb_strlen($date1)-3), 'php:n/j');
        } else if(strpos($ticket_name, $date2) !== false) {
            $ret = Yii::$app->formatter->asDate(mb_substr($date2,0,mb_strlen($date2)-3), 'php:n/j');
        } else if(strpos($ticket_name, $date3) !== false) {
            $ret = Yii::$app->formatter->asDate(mb_substr($date3,0,mb_strlen($date3)-3), 'php:n/j');
        } else if(strpos($ticket_name, $date4) !== false) {        
            $ret = Yii::$app->formatter->asDate(mb_substr($date4,0,mb_strlen($date4)-3), 'php:n/j');
        } else if(strpos($ticket_name, $date5) !== false) {        
            $ret = Yii::$app->formatter->asDate(mb_substr($date5,0,mb_strlen($date5)-4), 'php:n/j');
        } else if(strpos($ticket_name, $date6) !== false) {        
            $ret = Yii::$app->formatter->asDate(mb_substr($date6,0,mb_strlen($date6)-4), 'php:n/j');
        } else if(strpos($ticket_name, $date7) !== false) {
            $ret = Yii::$app->formatter->asDate(mb_substr($date7,0,mb_strlen($date7)-4), 'php:n/j');
        } else if(strpos($ticket_name, $date8) !== false) {
            $ret = Yii::$app->formatter->asDate(mb_substr($date8,0,mb_strlen($date8)-4), 'php:n/j');
        } else if(strpos($ticket_name, $date9) !== false) {
            $ret = Yii::$app->formatter->asDate(mb_substr($date9,0,mb_strlen($date9)-4), 'php:n/j');
        } else if(strpos($ticket_name, $date10) !== false) {
            $ret = Yii::$app->formatter->asDate(mb_substr($date10,0,mb_strlen($date10)-4), 'php:n/j');
        } else if(strpos($ticket_name, $date11) !== false) {
            $ret = Yii::$app->formatter->asDate(mb_substr($date11,0,mb_strlen($date11)-4), 'php:n/j');
        } else if(strpos($ticket_name, $date12) !== false) {
            $ret = Yii::$app->formatter->asDate(mb_substr($date12,0,mb_strlen($date12)-4), 'php:n/j');
        } else {
            return $ret;
        }


        // var_dump('return '.$ret, Yii::$app->formatter->asDate(mb_substr($ret,0,mb_strlen($ret)-3), 'php:n/j'));exit;
        return $ret;
    }
}

