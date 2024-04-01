<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/cart/Module.php $
 * $Id: Module.php 4249 2020-04-24 16:42:58Z mori $
 */

namespace frontend\modules\cart;

use Yii;
use \common\models\Company;
use \common\models\Subcategory;
use \common\models\WtbPurchase;
use \common\components\cart;
use common\models\Stock;
use common\models\Branch;
use backend\models\Staff;
use common\models\Product;
use common\models\LiveInfo;
use \common\models\Category;
use common\models\Payment;

class Module extends \yii\base\Module
{
    const TARGET_ADDR = 'addr';

    public $controllerNamespace = 'frontend\modules\cart\controllers';
    public $cart;
    public $customer;
    public $stock;

    private static $my_id = 'ConsumerCart';

    const COOKIE_ITEM_COUNT = 'cartItemCount';

    public function init()
    {
        $this->cart = new \common\components\cart\CartManager();
        $this->stock = new \common\models\Stock();

        if(! Yii::$app->session->get(self::$my_id, null))
             Yii::$app->session->set(self::$my_id, Yii::$app->session->id);
    }

    public static function flushItemCount()
    {
        if($row = WtbPurchase::findOne(Yii::$app->session->id))
            $row->delete();

        return Yii::$app->response->cookies->remove(self::COOKIE_ITEM_COUNT);
    }

    public static function getItemCount()
    {
        // get a cookie to fetch current itemCount
        return Yii::$app->request->cookies->getValue(self::COOKIE_ITEM_COUNT, 0);
    }

    public function beforeAction($action)
    {
        if (! parent::beforeAction($action))
            return false;

        $this->loadModel();
        $this->cart->validate();
        return true;
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        $this->saveModel();

        return $result;
    }

    /**
     * @return bool
     */
    public function addProduct($pid, $options)
    {
        $warning = null;
        $liveInfo = null;

        if(isset($options['info_id'])) {
            $liveInfo = LiveInfo::findOne($options['info_id']);
            $error = "";

            if('0' == $options['school'])
                $error .= "会場を選択してください<br/>";

             // オンライン時のクーポン適用不可がONになっている場合
            if($liveInfo->online_coupon_enable == 0){
                if(isset($options['coupon']) && $options['coupon'] != "" && '99' == $options['school']) {
                    $error .= "自宅受講にはクーポンを適用できません<br/>";
                }
             }

             // オンライン時のオプション適用不可がONになっている場合
            if($liveInfo->online_option_enable == 0){
                if(isset($options['option']) && $options['option'] == "1" && '99' == $options['school']) {
                    $error .= "自宅受講ではオプションを購入できません<br/>";
                }
            }             

            if(isset($options['coupon']) && $options['coupon'] != "" && $liveInfo->coupon_code != $options['coupon']) {
                $error .= "入力されたクーポンコードは無効です<br/>";
                unset($options['coupon']);
            }

            if($error != "") {
                Yii::$app->getSession()->addFlash('warning',$error);
                return false;                 
            }
        }

//        if(($pid == '2290' || $pid == 2291) && '0' == $options['school']) {
        if(($pid == '2290' || $pid == 2291 || $pid == '2292' || $pid == 2293 || $pid == 2342 || $pid == 2343 || $pid == 2344 || $pid == 2345 || $pid == 2346 || $pid == 2347 || $pid == 2348) && '0' == $options['school']) {
           Yii::$app->getSession()->addFlash('warning', sprintf("会場を選択してください"). '<br/>');
           return false;
        }
//print"ssssss".$params['customer_msg'];
//print"ssssss";print $options['line_coupon'];exit;
        if($pid == '2290' || $pid == '2291') {
            if($options['school'] == '99') {
                $pid = 2290;
                unset($options['lunch_210227']);
            } else {
//print $pid;exit;
                $pid = 2291;
            }
        }
        if($pid == '2342' || $pid == 2343 || $pid == 2344 || $pid == 2345 || $pid == 2346 || $pid == 2347 || $pid == 2348) {
            if($options['school'] == '99') {
                $pid = 2345;
                unset($options['lunch_210417']);
            } else {
                switch($options['school']) {
                  case '東京':
                    $pid = 2342;
                    if($options['line_coupon']){
                      $options['school'] = '東京＋LINEクーポン';
                      $pid = 2346;
                    }
                    break;

                  case '大阪':
                    $pid = 2342;
                    if($options['line_coupon']){
                      $options['school'] = '大阪＋LINEクーポン';
                      $pid = 2346;
                    }
                    break;

                  case '東京＋ワークショップキット':
                    $pid = 2343;
                    if($options['line_coupon']){
                      $options['school'] = '東京＋ワークショップキット＋LINEクーポン';
                      $pid = 2347;
                    }
                    break;

                  case '大阪＋ワークショップキット':
                    $pid = 2344;
                    if($options['line_coupon']){
                      $options['school'] = '大阪＋ワークショップキット＋LINEクーポン';
                      $pid = 2348;
                    }
                    break;

                  case '99':
                    $pid = 2345;
                    break;

                  case '東京＋LINEクーポン':
                  case '大阪＋LINEクーポン':
                    $pid = 2346;
                    break;

                  case '東京＋ワークショップキット＋LINEクーポン':
                    $pid = 2347;
                    break;

                  case '大阪＋ワークショップキット＋LINEクーポン':
                    $pid = 2348;
                    break;


                  default:
                    $pid = 2342;
                }
            }
        }


        if(($pid == 2346 || $pid == 2347 || $pid == 2348) && !$options['line_coupon']) {
           Yii::$app->getSession()->addFlash('warning', sprintf("LINEクーポンの入力がありません。入力をお確かめ下さい。"). '<br/>');
           return false;
        }
        if(($pid == '2342' || $pid == 2343 || $pid == 2344 || $pid == 2345 || $pid == 2346 || $pid == 2347 || $pid == 2348) && isset($options['line_coupon']) && (($options['line_coupon'] && 'BJ3P7s' != $options['line_coupon']) && ($options['line_coupon'] && 'BJ3P9s' != $options['line_coupon']))) {
           Yii::$app->getSession()->addFlash('warning', sprintf("そのLINEクーポンコードは存在しません。入力をお確かめ下さい。"). '<br/>');
           return false;
        }


        $product = Product::findOne($pid);

        // HP商品はカート追加できないようにする
        if(Company::PKEY_HP == $product->seller->company_id) {
            Yii::$app->getSession()->addFlash('warning', 'この商品の取り扱いは終了しました。');
            return true;
        }
        if(Company::PKEY_TROSE == $product->seller->company_id)
            $item = $this->addTroseProduct($product, $options);
        else
            $item = $this->cart->addProduct($pid, $options);

        if( $item ) {
            // ライブ配信チケット用処理を入れる 2020/04/22 : kawai
            $price = $item->price;

            if('echom-frontend' == Yii::$app->id) {
                $customer = \common\models\Customer::findOne(Yii::$app->user->id);
                // $grade_id = isset(Yii::$app->user->id) ? \common\models\Customer::currentGrade(Yii::$app->user->id) : null;
                $grade = \common\models\ProductGrade::getGrade($item->model->product_id,$customer);
                $price = isset($grade->price) ? $grade->price : $item->price;
            }

            Yii::$app->getSession()->addFlash('success', sprintf("%s &yen;%s が %d 点 カートに入りました", $item->name, number_format($price), $options['qty']). '<br/>' . \yii\helpers\Html::a("カートを見る",['/cart']));
        } else {
            if(Company::PKEY_TY == $product->seller->company_id) {
                if($pid == Product::PKEY_OSECHI) {
                    Yii::$app->getSession()->addFlash('warning', sprintf("%s は 他の商品と同時に購入することはできません", $product->name). '<br/>');
                } else {
                    $items = $this->cart->carts[1]->items;
                    foreach($items as $item) {
                        if($item->model->product_id == Product::PKEY_OSECHI) {
                            Yii::$app->getSession()->addFlash('warning', sprintf("%s は %s と同時に購入することはできません", $product->name, \common\models\Product::findOne(Product::PKEY_OSECHI)->name, $options['qty']). '<br/>');
                            break;
                        }
                    }
                }
            }
       }

        return $item ? true : false;
    }

    /* @return false | ProductItem */
    private function addTroseProduct($product, $options)
    {
        $warning = null;
        $q1 = $product->getSubcategories()
                      ->andWhere(['or',
                                  ['parent_id' => Subcategory::PKEY_TROSE_SIZE],
                                  ['parent_id' => Subcategory::find()->select('subcategory_id')
                                                                     ->where(['parent_id' => Subcategory::PKEY_TROSE_SIZE])],
                      ]);
        $q2 = $product->getSubcategories()
                      ->andWhere(['parent_id' => \common\models\Subcategory::PKEY_TROSE_COLOR]);

        if((1 < $q1->count()) && ! $options['sku_size'])
            $warning = '商品のサイズを指定してください';

        if((1 < $q2->count()) && ! $options['sku_color'])
            $warning = '商品の色を指定してください';

        if(isset($warning))
        {
            Yii::$app->session->addFlash('warning', $warning);
            return false;
        }

        if(($id = $options['sku_size']) &&
            ($size = \common\models\Subcategory::findOne($id)))
                $product->name .= sprintf('[%s]', $size->name);

        if(($id = $options['sku_color']) &&
           ($color = \common\models\Subcategory::findOne($id)))
               $product->name .= sprintf('[%s]', $color->name);

        $item = new \common\components\cart\ProductItem(['model'=>$product]);

        if(($qty = (int) $options['qty']) && (0 < $qty))
        {
            for($i = 0; $i < $qty; $i++)
                if(! $this->cart->appendCartItem($item))
                    return false;
        }
        else
            return false;

        return $item;
    }


    /**
     * @return bool
     */
    public function addRemedy($rid, $pid, $vid, $qty, $prange_id=null, $recipe_id = null)
    {
         if(Yii::$app->request->get('prange_id', null))
            $prange_id = Yii::$app->request->get('prange_id', null);

        $item = $this->cart->addRemedy($rid, $pid, $vid, $qty, $prange_id, $recipe_id);
        if($item)
        {
            Yii::$app->getSession()->addFlash('success', sprintf("%s &yen;%s が %d 点 カートに入りました", $item->name, number_format($item->price), $qty) . '<br/>' . \yii\helpers\Html::a("カートを見る",['/cart']));
        }
        return isset($item);
    }

    /**
     * @return bool
     */
    public function appendCartItem($model)
    {
        if($model->className() != \common\components\cart\ComplexRemedyForm::className())
            return false;

        if($this->cart->appendCartItem($model))
        {
            Yii::$app->getSession()->addFlash('success',
                                              sprintf("%s が カートに入りました", $model->name)
                                            . '<br>' . \yii\helpers\Html::a("カートを見る",['/cart'])
            );
            $items = $this->cart->getCart(0)->items;
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function del($cart_idx, $item_idx)
    {
        $item = $this->cart->del($cart_idx, $item_idx);

        if(false === $item) {
            Yii::$app->getSession()->addFlash('error', "削除できませんでした");
        } else {
            if(isset($_SESSION['companion_item']) && $_SESSION['companion_item'] == $item->model->product_id) {
                Yii::$app->session->remove('companion_info_id');
                Yii::$app->session->remove('companion_data');
                Yii::$app->session->remove('companion_price');
                Yii::$app->session->remove('companion_tax');
                Yii::$app->session->remove('companion_item');
                Yii::$app->session->remove('companion_subscription');
                Yii::$app->session->remove('live_notes_companion');
            }            

            if($item->category->category_id != Category::LIVE_OPTION) {
                Yii::$app->getSession()->addFlash('success',
                    sprintf("%s を削除しました", \yii\helpers\Html::a($item->name, $item->url))
                );
            } else {
                Yii::$app->getSession()->addFlash('success',
                    sprintf("%s を削除しました", $item->name));                
            }
        }

        return $item;
    }

        public function recipeDel($cart_idx, $recipe_id)
    {
        $result = $this->cart->recipeDel($cart_idx, $recipe_id);
        if(false === $result)
            Yii::$app->getSession()->addFlash('error', "削除できませんでした");
        else
            Yii::$app->getSession()->addFlash('success',
                sprintf("適用書 %06d を削除しました",$recipe_id)
            );

        return $result;
    }

    /**
     * @return bool
     */
    public function delRemedy($rid, $pid, $vid)
    {
        return $this->cart->delRemedy($rid, $pid, $vid);
    }

    /**
     * @return bool
     */
    public function finish($cart_idx)
    {
        $outOfStock = false;
        $msg = "注文を確定できませんでした。";
        $items = $this->cart->getCart($cart_idx)->items;
        // トランザクション開始
        $transaction = Yii::$app->db->beginTransaction();

        try {
            // サポート注文を考慮
            $result = true;
            $purchase = $this->cart->getCart($cart_idx)->purchase;
            // サポート注文が設定されている場合、代理注文者に付与するポイントを計算する
            $agent_point = 0;
            $grade_id = ($purchase->customer) ? $purchase->customer->grade_id : null;

            if($purchase->agent_id) {
                $agent = \common\models\Customer::findOne($purchase->agent_id);
                // 代理注文者にポイント付与
                $seller = \common\models\Customer::find()->where(['name01' => 'ポイント', 'name02' => '付与'])->one();
                $agent_pointing = new \common\models\Pointing([
                    'staff_id'     => 0, // システム自動処理
                    'customer_id'  => $purchase->agent_id, // 代理注文者に付与
                    'seller_id'    => $seller->customer_id, // 「ポイント付与」顧客固定
                    'total_charge' => 0,
                    'status'       => \common\models\Pointing::STATUS_SOLD,
                    'company_id'   => \common\models\Company::PKEY_HE, // サポート注文による注文の場合、一旦HEから代理注文者に付与する
                ]);
            }

            $ticket_id = [];
            foreach($items as $item)
            {
                if($purchase->agent_id) {
                    $direct_pointRate = $item->getPointRate();
                    $agent_pointRate = $purchase->getOfferRateByCustomer($item, $agent)['point'];
                    $agent_addPoint = (floor($item->price * $agent_pointRate / 100) - $item->getPointAmount());
                    $agent_addPoint = $agent_addPoint >= 0 ? $agent_addPoint : 0;
                    $agent_point += $agent_addPoint;
                }

                if (isset($item->model->product_id)) {
                    $d_product = \common\models\ProductDiscount::find()->where(['product_id' => $item->model->product_id])->one();
                    // 相談会チケット購入者は管理テーブルで情報を管理する
                    $sodan_query = \common\models\ProductSubcategory::find()->where(['ean13' => $item->code]);
                    $sodan_ticket = \yii\helpers\ArrayHelper::map($sodan_query->all(), 'ean13', 'subcategory_id');
                    if ($d_product) {
                        for ($i = 0; $i < $item->qty; $i++) {
                            $ticket = new \common\models\DiscountProductLog([
                                'customer_id' => $purchase->customer->customer_id,
                                'discount_id' => $d_product->discount_id,
                                'use_count'   => $d_product->use_count,
                                'subcategory_id' => (in_array(Subcategory::PKEY_SODAN_TICKET, $sodan_ticket)) ? Subcategory::PKEY_SODAN_TICKET : null
                            ]);
                            if ($ticket->save()) {
                                if (in_array(Subcategory::PKEY_SODAN_TICKET, $sodan_ticket)) {
                                    $ticket_id[] = $ticket->ticket_id;
                                }
                            } else {
                                $result = false;
                            }
                        }
                    }
                }

                if(($m = $item->model) && $m instanceof \yii\db\ActiveRecord)
                {
                    // 野菜セットMの場合、dtb_stock上の在庫確認を行う(併せてバージョン取得)
                    if ($m->hasAttribute('product_id')) {// && $m->product_id == Stock::VEGETABLE_SETM) {

                        // 野菜セットMの場合はStockテーブルの在庫数の取得、更新
                        $stock = $this->stock->getStock($m->product_id);

                        if (! $stock) continue;

                        if ($stock->actual_qty < 1 || ($stock->actual_qty < $item->qty)) {
                            if ($stock->actual_qty != 0 && $stock->actual_qty < $item->qty) {
                                $msg .= sprintf("<br> %s の在庫数を上回る数量が指定されています。(現在の在庫数：%s)", $m->name, $stock->actual_qty);
                            }

                            $m->in_stock = 0;
                        } else {
                            // 在庫引当
                            $stock->actual_qty = ($stock->actual_qty - $item->qty);
                            $stock->updated_by = Staff::STAFF_SYSTEM; // 暫定でフロント側で在庫が更新される場合は「システム」にする

                            // mvtb_product_masterの更新日時を更新( キャッシュ対応 )
                            $q = Yii::$app->db
                            ->createCommand('update mvtb_product_master set update_date = NOW() where product_id = :product_id')
                            ->bindValues([':product_id'=> $m->product_id]);

                            if (! $stock->save(false) || ! $q->execute()) {
                                Yii::error(
                                    sprintf('dtb_stock と mvtb_product_master の更新処理に失敗しました。(product_id: %s)', $m->product_id)
                                    ,self::className().'::'.__FUNCTION__);
                                $m->in_stock = 0;
                            }
                        }
                    }                    

                    if (($m->hasAttribute('in_stock') && ! $m->in_stock))
                        $outOfStock = true;
                }
            }

            if(! $outOfStock && $purchase_id = $this->cart->save($cart_idx))
            {
                // カートが保存出来たら次はポイント付与を確定させる
                if($purchase->agent_id && $agent_point > 0) {
                    $agent_pointing->point_given = $agent_point;
                    $agent_pointing->note = "サポート注文 伝票NO：".$purchase->purchase_id;
                    if(!$agent_pointing->save())
                        $result = false;
                }                

                // 異常が無ければ最終確定
                if($result) {
                    $transaction->commit();
                    // カートの会社名を取得
                    $cart_name = $this->cart->getCart($cart_idx)->name;
                    $label = sprintf('「マイページ ご購入の履歴　注文番号 %06d」', $purchase_id);

                    // 備考欄を保持していたセッション情報を削除する
                    if(isset($_SESSION['live_notes']))
                        unset($_SESSION['live_notes']);

                    // サポート注文の場合はメッセージを変更する。伝票にアクセスできないため。
                    if(Yii::$app->user->identity && Yii::$app->user->identity->id == $purchase->agent_id) {
                        Yii::$app->session->addFlash('success',
                            sprintf('ご注文ありがとうございます<br>「'.$cart_name.'」のカートでのサポート注文が確定いたしました。<br>ご注文内容はメールにてご確認いただけます。')
                        );
                    } else {
                        if('echom-frontend' == Yii::$app->id) {

                            // クレジット決済ならcredit.localtoyouke.com にリダイレクトさせる
                            if($purchase->payment_id == \common\models\Payment::PKEY_CREDIT_CARD) {
                                return true;
                            }

                            Yii::$app->session->addFlash('success',
                                sprintf('ご注文ありがとうございます<br>注文内容をご確認ください。また、同じ内容を記載したメールをお客様宛にお送りしております。')
                            );

                        } else {
                            Yii::$app->session->addFlash('success',
                                sprintf('ご注文ありがとうございます<br>「'.$cart_name.'」のカートでのご注文が確定いたしました。<br>ご注文内容は、%sでご確認いただけます。',
                                        Yii::$app->user->isGuest || (Company::PKEY_HJ == $cart_idx /* RecipeCart */)
                                        ? $label
                                        : \yii\helpers\Html::a($label, ['/profile/history/view', 'id'=>$purchase_id])
                                )
                            );
                        }
                    }

                    if(isset($this->cart->activeCarts) && 1 < count($this->cart->activeCarts)) {
                        $cart_names = [];
                        foreach($this->cart->activeCarts as $activeCart) {
                            if($cart_name != $activeCart->name) {
                                $cart_names[] = $activeCart->name;
                            }
                        }
                        Yii::$app->session->addFlash('error',implode('、',$cart_names).'でのご注文が未確定です。');
                    }
                    if($purchase = \common\models\Purchase::findOne($purchase_id))
                    {
                        if('echom-frontend' == Yii::$app->id) {
                            $mailer = new \common\components\sendmail\ECHomPurchaseMail(['model'=>$purchase]);
                        } else {
                            $mailer = new \common\components\sendmail\PurchaseMail(['model'=>$purchase]);
                        }
                        $mailer->thankyou();
                        if ($ticket_id) {
                            $mailer->ticket($ticket_id);
                        }
                    }
                    else
                        Yii::error(sprintf('thankyou mail was not sent for Purchase::findOne(%d) failed',$purchase_id),self::className().'::'.__FUNCTION__);

                    return true;
                }
            }
            $transaction->rollback();

            Yii::$app->getSession()->addFlash('error', $msg);

            Yii::error([
                "注文を確定できませんでした",
                'source'=>$this->className().'::'.__FUNCTION__,
                'errors'=>$this->cart->getErrors(),
            ]);


        } catch (StaleObjectException $e) {
            // 衝突を解決するロジック
            $transaction->rollback();
            Yii::$app->getSession()->addFlash('error', $msg);
        }


        return false;
    }

    /**
     * @return bool
     */
    public function loadModel()
    {
        if(Yii::$app->user->isGuest)
            $this->customer = new \common\models\Customer([
                'email' => Yii::$app->session->get('email')
            ]);
        else
            $this->customer = Yii::$app->user->identity;

        $this->cart->setCustomer($this->customer);

        $row = WtbPurchase::findOne(Yii::$app->session->id);
        if(! $row)
            return true;

        $data = json_decode($row->data, true); // convert to array
        foreach ($data as $key => $cart) {
            if(isset($cart['purchase']['agent_id']) && isset($cart['delivery']['code'])) {
/*
                $customer = \common\models\Customer::findOne([
                    'customer_id' => $cart['purchase']['agent_id']
                ]);
*/
                $customer = \common\models\Customer::findByBarcode($cart['delivery']['code']);

                if($customer) {
                    $this->cart->carts[$key]->setCustomer($customer);
                    //$this->cart->setCustomer($customer);
                }
            }
        }
        return $this->cart->feed($data);
    }

    /**
     * @return boolean : save() succeeded or not
     */
    public function saveModel()
    {
        $row = WtbPurchase::findOne(Yii::$app->session->id);
        if(! $row)
        {
            $row = new WtbPurchase();
            $row->session = Yii::$app->session->id;
        }
        $row->data = json_encode($this->cart->dump());

        // set a cookie to fetch current itemCount
        Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name'  => self::COOKIE_ITEM_COUNT,
            'value' => $this->cart->itemCount,
        ]));

        return $row->save();
    }

    /* @return bool */
    public static function updateSession($prev_id, $params = null)
    {
        if(0 == self::getItemCount())
            return true; // no need to update

        // セッションに対してpayment_id変更をかける場合
        if(isset($params['payment_id']))
            return WtbPurchase::updatePaymentSession($prev_id, $params['payment_id']);

        return WtbPurchase::updateSession($prev_id);
    }

    /* @return bool */
    public function updateAddr($cart_idx, $model)
    {
        return $this->cart->setDestination($cart_idx, $model);
    }

    /* @return bool */
    public function updateDateTime($cart_idx, $date, $time_id)
    {
        return $this->cart->updateDateTime($cart_idx, $date, $time_id);
    }

    /* @return bool */
    public function updateCampaign($cart_idx, $params)
    {
        if(! isset($params['PurchaseForm']) || ($params['PurchaseForm'] < 0) || ! Yii::$app->user->identity)
            throw new \yii\web\BadRequestHttpException("パラメタが不正です");

        $campaign_code = $params['PurchaseForm']['campaign_code'];
        $ret = $this->cart->updateCampaign($cart_idx, $campaign_code);
        if(! $ret)
            Yii::$app->getSession()->addFlash('error', "キャンペーンを適用できませんでした");
        else
            Yii::$app->getSession()->addFlash('success', "キャンペーンを適用しました");
    }

    /* @return bool */
    public function deleteCampaign($cart_idx, $params)
    {
        if(! Yii::$app->user->identity)
            throw new \yii\web\BadRequestHttpException("パラメタが不正です");

        $ret = $this->cart->deleteCampaign($cart_idx);
        if(! $ret)
            Yii::$app->getSession()->addFlash('error', "キャンペーンを解除できませんでした");
        else
            Yii::$app->getSession()->addFlash('warning', "キャンペーンを解除しました");

    }

    public function unsetCampaign($cart_idx)
    {

        $ret = $this->cart->unsetCampaign($cart_idx);
        if(! $ret)
            Yii::$app->getSession()->addFlash('error', "キャンペーンを解除できませんでした");
        else
            Yii::$app->getSession()->addFlash('warning', "キャンペーンを解除しました");


    }

    /* @return bool */
    public function updateAgent($cart_idx, $params)
    {
        if(! isset($params['PurchaseForm']) || ($params['PurchaseForm'] < 0) || ! Yii::$app->user->identity)
            throw new \yii\web\BadRequestHttpException("パラメタが不正です");
        $direct_code = $params['PurchaseForm']['direct_code'];
        $ret = $this->cart->updateAgent($cart_idx, $direct_code);
        if(! $ret) {
            Yii::$app->getSession()->addFlash('error', "サポート注文を適用できませんでした");
        } else {
            $ret = $this->cart->updatePayment($cart_idx,Payment::PKEY_SUPPORT);            
            $this->saveModel();

            if(! $this->updateAgentSession($params, $cart_idx)) {
                 Yii::warning('updateSession() failed', $this->className().'::'.__FUNCTION__);
            }

            Yii::$app->getSession()->addFlash('success', "サポート注文を適用しました");
        }
    }

    public function unsetAgent($cart_idx,$params)
    {

        $ret = $this->cart->unsetAgent($cart_idx);
        if(! $ret) {
            Yii::$app->getSession()->addFlash('error', "サポート注文を解除できませんでした");
        } else {
            $this->saveModel();

            if(! $this->updateAgentSession($params, $cart_idx)) {
                 Yii::warning('updateSession() failed', $this->className().'::'.__FUNCTION__);
            }

            Yii::$app->getSession()->addFlash('warning', "サポート注文を解除しました");
        }

    }

    /**
     * サポート注文設定変更時にセッション内のpayment_idを変更する
     **/
    public function updateAgentSession($params, $cart_idx=0)
    {
        $prev_id = Yii::$app->session->id;
        $purchase = $this->cart->getCart($cart_idx)->purchase;
        if($purchase->agent_id) {
            $params['payment_id'] = \common\models\Payment::PKEY_SUPPORT;
        } else {
            // ここで代理店ユーザーならデフォルト支払いは代引きではない
            $customer = $this->cart->getCart($cart_idx)->customer;
            if((\common\models\CustomerGrade::PKEY_AA <= $customer->grade_id) && isset($customer->ysdAccount->detail))
            {
                $params['payment_id'] = \common\models\Payment::PKEY_DIRECT_DEBIT;
            }
            elseif($customer->isAgency())
            {
                $params['payment_id'] = \common\models\Payment::PKEY_BANK_TRANSFER;
            }
        }

        // セッションに対してpayment_id変更をかける場合
        if(isset($params['payment_id'])) {
            $this->cart->updatePayment($cart_idx, (int) $params['payment_id']);
            return WtbPurchase::updatePaymentSession($prev_id, $params['payment_id']);
        }
        return WtbPurchase::updateSession($prev_id);
    }

    /* @return bool */
    public function updateGift($cart_idx, $params)
    {
        if(! isset($params['gift']))
            throw new \yii\web\BadRequestHttpException("パラメタが不正です");

        if(!isset($this->customer->ysdAccount) || !$this->customer->ysdAccount->isValid()/* 口座振替が有効になっていない人 */) {
            if(! $this->customer->isAgency())
                throw new \yii\web\BadRequestHttpException("現在の設定では納品書金額表示指定はご利用できません");
        }

        $bool = 1 == $params['gift'] ? true : false;

        if(! $this->cart->updateGift($cart_idx, $bool))
            Yii::$app->getSession()->addFlash('error', "申し訳ありませんが、納品書金額表示の設定ができませんでした");
        else
            Yii::$app->getSession()->addFlash('success',
                 $this->cart->getCart($cart_idx)->isGift() ? "納品書金額表示を「非表示」に設定しました" : "納品書金額表示を「表示」に設定しました"
            );
    }

    /* @return bool */
    public function updateMsg($cart_idx, $params)
    {
        if(! isset($params['customer_msg']) || is_array($params['customer_msg']))
            throw new \yii\web\BadRequestHttpException("パラメタが不正です");

        $ret = $this->cart->updateMsg($cart_idx, $params['customer_msg']);

        if($ret)
            Yii::$app->getSession()->addFlash('success', "メッセージを更新しました");
    }

    public function updateCompanion($cart_idx, $params)
    {
        if(! isset($params['PurchaseForm']['note']) || is_array($params['PurchaseForm']['note']))
            throw new \yii\web\BadRequestHttpException("パラメタが不正です");

        $cart  = $this->cart->carts[$cart_idx];
            
        $purchase = $cart->purchase;
        $notes = isset($_SESSION['live_notes']) ? $_SESSION['live_notes'] : [];
        $pid = $params['companion_item'];

        if(isset($params['companion_info_id'])) {
            $notes[$pid] = $this->cart->getLiveNote($pid, $params);
            $_SESSION['live_notes'] = $notes;
            $note = implode(" ",array_values($notes));
            $purchase->note = $note;
        }

        if(count($purchase->items) == 0) {
            if(in_array($pid, [1765,1766,1767,1768,1769,1770,1771,1772,1775,1776,1777,1778,1922,1954,1955,1966,1967,2291,2342,2343,2344,2345,2346,2347,2348])) {
                $notes[$pid] = $this->getLiveNote($pid, $params);
            }
        }

        foreach($purchase->items as $k => $i)
        {
            if(in_array($pid, [1765,1766,1767,1768,1769,1770,1771,1772,1775,1776,1777,1778,1922,1954,1955,1966,1967,2291,2342,2343,2344,2345,2346,2347,2348])) {
                $notes[$pid] = $this->getLiveNote($pid, $params);
            }

        }
        $_SESSION['live_notes'] = $notes;
        $note = implode(" ",array_values($notes));

        $ret = $this->cart->updateNote($cart_idx, $note);

        if($ret)
            Yii::$app->getSession()->addFlash('success', "同行者情報を更新しました");
    }


    /* @return void */
    public function updatePayment($cart_idx, $params)
    {
        if(null === ($cart = $this->cart->getCart($cart_idx)))
           throw new \yii\web\BadRequestHttpException("ご指定のカートは存在しません");
        if(count($cart->payments) <= 1)
           throw new \yii\web\BadRequestHttpException("ご指定のカートでは支払い方法を変更できません");

        if(! isset($params['payment']) || ($params['payment'] < 0) ||
           ! in_array($params['payment'], $cart->payments))
        {
            Yii::error(sprintf('posted payment %d not in array', $params['payment']), self::className().'::'.__FUNCTION__);
            throw new \yii\web\BadRequestHttpException("パラメタが不正です");
        }

        $ret = $this->cart->updatePayment($cart_idx, (int) $params['payment']);

        if(! $ret) {
            Yii::$app->session->addFlash('error', "支払い方法が適用できませんでした");
        } else {
            // 代引きが選択された場合、納品書金額表示の設定を「表示」に強制的に戻す
            if((int)$params['payment'] == \common\models\Payment::PKEY_YAMATO_COD) {
                $params['gift'] = 0;
                $this->updateGift($cart_idx, $params);
            }

            Yii::$app->session->addFlash('success', "支払い方法を適用しました");
        }
        return $ret;
    }

    /* @return void */
    public function updatePointConsume($cart_idx, $params)
    {
        if(! isset($params['point']) || ($params['point'] < 0) || ! Yii::$app->user->identity)
            throw new \yii\web\BadRequestHttpException("パラメタが不正です");

        if(Yii::$app->user->identity->point < $params['point'])
        {
            Yii::$app->getSession()->addFlash('error', sprintf("指定されたポイント(%s)は上限(%s)を超えています。",number_format($params['point']),number_format(Yii::$app->user->identity->point)));
            return;
        }

        $ret = $this->cart->updatePointConsume($cart_idx, (int) $params['point']);

        if(! $ret)
            Yii::$app->getSession()->addFlash('error', "ポイント値引きが適用できませんでした");
        else
            Yii::$app->getSession()->addFlash('success', "ポイント値引きを適用しました");
    }

    public function updateQty($cart_idx, $params)
    {
        if(! isset($params['idx']) || ! isset($params['qty']) || ($params['idx'] < 0))
            throw new \yii\web\BadRequestHttpException("パラメタが不正です");

        $idx = mb_convert_kana($params['idx'], 'n', Yii::$app->charset);
        $qty = mb_convert_kana($params['qty'], 'n', Yii::$app->charset);

        if($qty <= 0)
        {
            $item = $this->cart->del($cart_idx, $idx);
            if(false === $item)
                Yii::$app->getSession()->addFlash('error', "削除できませんでした");
            else
                Yii::$app->getSession()->addFlash('success',
                    sprintf("%s を削除しました", \yii\helpers\Html::a($item->name, $item->url))
                );

            return $item;
        }

        $item = $this->cart->updateQty($cart_idx, $idx, $qty);
        if($item === false)
            Yii::$app->getSession()->addFlash('error', "数量が更新できませんでした");
        else
            Yii::$app->getSession()->addFlash('success',
                sprintf("%s が %d 点になりました", \yii\helpers\Html::a($item->name, $item->url), $item->qty)
            );

        return $item;
    }

}

