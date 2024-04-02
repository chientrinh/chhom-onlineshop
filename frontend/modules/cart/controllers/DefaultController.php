<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/modules/cart/controllers/DefaultController.php $
 * $Id: DefaultController.php 3028 2016-10-27 07:54:03Z mori $
 */

namespace frontend\modules\cart\controllers;

use Yii;
use \common\models\CustomerGrade;
use \common\models\Payment;
use \common\models\Purchase;
use common\models\Stock;
use common\models\LiveInfo;

class DefaultController extends \yii\web\Controller implements \yii\base\ViewContextInterface
{
    const TARGET_ADDRBOOK= 'addrbook';
    const TARGET_ADDRESS = 'address';
    const TARGET_CAMPAIGN = 'campaign';
    const TARGET_CAMPAIGN_DEL = 'campaign-del';
    const TARGET_AGENT = 'agent';
    const TARGET_AGENT_DEL = 'agent-del';
    const TARGET_DATE    = 'date';
    const TARGET_GIFT    = 'gift';
    const TARGET_MSG     = 'msg';
    const TARGET_PAYMENT = 'payment';
    const TARGET_POINT   = 'point';
    const TARGET_QTY     = 'qty';
    const TARGET_COMPANION = 'companion';

    public $defaultAction = 'index';
    private $_backUrl;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions'=> ['index'],
                        'allow'  => true,
                        'verbs'  => ['GET'],
                    ],
                    [
                        'actions'=> ['add','del','update','recipedel'],
                        'allow'  => true,
                        'verbs'  => ['GET','POST'],
                    ],
                    [
                        'actions'=> ['finish', 'agreement', 'text-agreement','result','complete'],
                        'allow'  => true,
                        'verbs'  => ['GET','POST'],
                    ],
                    [
                        'allow'  => false, // everything else is denied
                    ],
                ],
            ],
        ];
    }

    public function init()
    {
        parent::init();

        $this->_backUrl = Yii::$app->request->referrer
                        ? Yii::$app->request->referrer
                        : \yii\helpers\Url::toRoute(sprintf('/%s/%s/index', $this->module->id, $this->module->defaultRoute));
    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        if(('index' == $action->id) ||
           (Yii::$app->request->isGet && ('update' == $action->id) && ('payment' == Yii::$app->request->get('target')))
        )
            $this->applyPaymentFilter();

        // 値引き情報のチェック
        $this->checkDiscount();

        return true;
    }

    public function actionIndex($cart_idx=null)
    {
        $support_entry = false;

        $carts = $this->module->cart->activeCarts;

        if(0 == count($carts))
        {
            // show empty cart
            $cart_idx = 0;
            $carts[$cart_idx]  = $this->module->cart->getCart($cart_idx);
        }
        if(! in_array($cart_idx, array_keys($carts)))
            $cart_idx = min(array_keys($carts));
        else
            $cart_idx = (int) $cart_idx;

        $items = $carts[$cart_idx]->items;
        foreach($items as $item)
        {
            $liveItemInfo = \common\models\LiveItemInfo::find()->where(['product_id'=>$item->model->product_id])->one();
            $liveInfo = $liveItemInfo ? $liveItemInfo->info : null;
            // カートにサポート申込対応チケットが入っている
            if(isset($liveInfo) && $liveInfo->support_entry) {
                $support_entry = $liveInfo->support_entry;
                break;
            }
        }


        $purchase = $carts[$cart_idx]->getPurchase();
//        var_dump($purchase->customer->name);exit;
        if($this->module->customer && isset($purchase)) {
            $campaign = $purchase->campaign;
            if($campaign && $campaign->status == 0){
                $this->module->unsetCampaign($cart_idx);
            }
        }

        $index = 'index';
        if('echom-frontend' == Yii::$app->id)
            $index = 'echom/index';

        return $this->render($index, [
            'customer' => $this->module->customer,
            'carts'    => $carts,
            'cart_idx' => $cart_idx,
            'support_entry' => $support_entry            
        ]);
    }

    public function actionAdd($pid, $qty=1)
    {
        $text = \common\models\TicketAgreement::find()->where(['product_id' => $pid])->one();
        // 同意がとれていない場合は同意確認画面へ
        if ($text && !(Yii::$app->request->get('agreed'))) {
            return $this->redirect('text-agreement?pid=' . $pid);
        }

        $qty = (int) trim(mb_convert_kana($qty, 'ns'));
        if($qty <= 0)
            throw new \yii\base\UserException("追加数量($qty)が不正です");

        $param = [
            'qty'       => $qty,
            'sku_color' => Yii::$app->request->get('sku_color', null),
            'sku_size'  => Yii::$app->request->get('sku_size',  null),
            'children' => Yii::$app->request->get('children', null),
            'lunchbox_200606' => Yii::$app->request->get('lunchbox_200606', null),
            'lunchbox_200607' => Yii::$app->request->get('lunchbox_200607', null),
            'lunch_200606' => Yii::$app->request->get('lunch_200606', null),
            'lunch_200607' => Yii::$app->request->get('lunch_200607', null),
            'info_id' => Yii::$app->request->get('info_id', null), // 2021/04/15
            'school' => Yii::$app->request->get('school', null),
            'lump_sum' => Yii::$app->request->get('lump_sum', null), // 2021/05/14
            'school_0' => Yii::$app->request->get('school_0', null), // 2021/05/14
            'school_1' => Yii::$app->request->get('school_1', null), // 2021/05/14
            'school_2' => Yii::$app->request->get('school_2', null), // 2021/05/14
            'coupon' => Yii::$app->request->get('coupon', null), // 2021/04/15
            'option' => Yii::$app->request->get('option', null), // 2021/04/15
            'online_coupon_enable' => Yii::$app->request->get('online_coupon_enable', null),            
            'online_option_enable' => Yii::$app->request->get('online_option_enable', null),            
            'line_coupon' => Yii::$app->request->get('line_coupon', null), //2294 - 2300
            'lunch_201003' => Yii::$app->request->get('lunch_201003', null),
            'lunch_201004' => Yii::$app->request->get('lunch_201004', null),
            'school1003' => Yii::$app->request->get('school1003', null),
            'school1004' => Yii::$app->request->get('school1004', null),
            'school1' => Yii::$app->request->get('school1', null),
            'school2' => Yii::$app->request->get('school2', null),
            'lunch_210227' => Yii::$app->request->get('lunch_210227', null),
//2293
            'lunch_210417' => Yii::$app->request->get('lunch_210417', null),
        ];

        $companion = Yii::$app->request->get('companion', null);
        if($companion && $companion != "[]") {
            $liveInfo = LiveInfo::findOne($param['info_id']);
            $capacity = $liveInfo->capacity;
            $left = $capacity - $liveInfo->subscription;

            $companion = json_decode($companion, true);
            $companion_text = "";
            $companion_type = "";
            $companion_price = 0;
            $companion_tax = 0;
            $companion_subscription = count($companion);
            if($companion_subscription > $left) {
                $over = $companion_subscription - $left;
                Yii::$app->getSession()->addFlash('error',"申込人数が定員を".$over."人分オーバーしています<br/>");
                $this->redirect($this->_backUrl);
                return false;
            }

            for($i=0; $i<count($companion); $i++) {
                $companion_type = $companion[$i][0];
                $companion_text .= $companion_type == "本人" ? "大人・" : $companion_type."・";
                $companion_price += $companion[$i][1];
                $companion_tax += $companion[$i][4];
                $companion_text .= $companion[$i][2];
                $companion_text .= $companion[$i][3];
                if($i+1 >= 1 && $i < count($companion)-1)
                    $companion_text .= ' ';
            }
            $companion_price = $companion_price - $companion_tax;
                        // イベント同行者 2023/04/12
            $param['companion_info_id'] = $param['info_id'] ? $param['info_id'] : null;
            $param['companion_data'] = $companion ? $companion : null;
            $param['companion'] = $companion_text ? $companion_text : null;
            $param['companion_item'] = $pid;
            $param['companion_price'] = $companion_price ? $companion_price : null; 
            $param['companion_tax'] = $companion_tax ? $companion_tax : null;
            $param['capacity'] = $capacity ? $capacity : null;
            $param['companion_subscription'] = $companion_subscription ? $companion_subscription : null;
        }

        // 一括購入用処理 2021/05/14
        if($param['school_0'] == "0" || $param['school_1'] == "0" || $param['school_2'] == "0") 
            Yii::$app->getSession()->addFlash('warning',"会場を選択してください<br/>");
            $this->redirect($this->_backUrl);   

        if($param['school_0'] && $param['school_0'] != "0") {
            // オンライン時のクーポン適用不可
            $liveInfo = LiveInfo::findOne($param['info_id']);
            if($liveInfo->online_coupon_enable == 0){
                if(isset($param['coupon']) && $param['coupon'] != "" && ('99' == $param['school_0'] || '99' == $param['school_1'] || '99' == $param['school_2'])) {
                    Yii::$app->getSession()->addFlash('warning',"自宅受講にはクーポンを適用できません<br/>");
                    unset($param['coupon']);
                    $this->redirect($this->_backUrl); 
                }
            }
            if(isset($param['coupon']) && $param['coupon'] != "" && $liveInfo->coupon_code != $param['coupon']) {
                Yii::$app->getSession()->addFlash('warning',"入力されたクーポンコードは無効です<br/>");

                unset($param['coupon']);
                $this->redirect($this->_backUrl); 
            }
#if($param['pid'] == '2447'){
#print "#####";exit;
#}

            $param1 = $param;
            if($param['coupon'])
                $param1['coupon'] = null;

            $param1['school'] = $param['school_0'];
            $this->module->addProduct(2444,$param1);
#if($param['pid'] == '2447'){
            $param2 = $param;
            if($param['coupon'])
                $param2['coupon'] = null;
 
            $param2['school'] = $param['school_1'];
            $this->module->addProduct(2445,$param2);
#}
            $param3 = $param;
            $param3['school'] = $param['school_2'];

            if($param['coupon'])
                $param3['coupon'] = $param['coupon'];

            $this->module->addProduct(2446,$param3);

            // ３つのチケットが一括して入った時、会員ランクに応じて値引きする
            // TODO: 決め打ちでなくシステム上で管理する場合、一括購入数の設定をした上で、ForEachで回すなど改修か
            $lump_sum = isset($_SESSION['lump_sum']) ? $_SESSION['lump_sum'] : "";
            $cart = $this->module->cart->getCart(0); // カートを取得
            $purchase = $cart->purchase;
            $customer = $cart->customer;

            if(count($cart->items) >= 3 && $param['lump_sum'] && $param['lump_sum'] == "1") {
                $discount = $this->getLumpSumDiscount($customer);
                $purchase->discount += $discount;
                $purchase->note .= " 一括購入値引き適用";
            }

            $this->redirect($this->_backUrl);
            return;
        }


        if($this->module->addProduct($pid, $param)) {
            if($param['option'] == "1" && $param['info_id']) {
                // オプション用商品を検索してカートに追加する
                $liveInfo = LiveInfo::findOne($param['info_id']);
                if($liveInfo) {
                    $option_product_id = $liveInfo->product_id;
                    $params = ['qty' => $qty];
                    $this->module->addProduct($option_product_id, $params);
                }

            }
        }

        if(Yii::$app->request->isAjax &&
           Yii::$app->session->removeFlash('success'))
               return $qty; // ok

        if (Yii::$app->request->get('sp_product')) {
            //特典商品一覧画面に飛ばす
            $this->redirect(['/ty/subcategory', 'id' => \common\models\Subcategory::PKEY_MAGAZINE_CAMPAIGN]);
            return;
        }
        $this->redirect($this->_backUrl);
    }

    /**
     * @param $c cart index
     * @param $i item index
     */
    public function actionDel($cart_idx, $item_idx)
    {
        $this->module->del($cart_idx, $item_idx);

        $support_entry = false;
        $cart = $this->module->cart->getCart($cart_idx);
        $items = $cart->items;
        $purchase = $cart->getPurchase();
        foreach($items as $item)
        {
            $liveItemInfo = \common\models\LiveItemInfo::find()->where(['product_id'=>$item->model->product_id])->one();
            $liveInfo = $liveItemInfo ? $liveItemInfo->info : null;
            // カートにサポート申込対応チケットが入っている
            if(isset($liveInfo) && $liveInfo->support_entry) {
                $support_entry = $liveInfo->support_entry;
                break;
            }
        }
        if($purchase->agent_id && !$support_entry)
            $this->module->unsetAgent($cart_idx, "");
        

        $this->redirect($this->_backUrl);
    }
    
    public function actionRecipedel($cart_idx,$recipe_id)
    {
        $this->module->recipeDel($cart_idx, $recipe_id);

        $this->redirect($this->_backUrl);
    }
    

    public function actionUpdate($cart_idx, $target)
    {
        if(! $cart = $this->module->cart->getCart($cart_idx))
            throw new \yii\web\NotFoundHttpException('invalid param: cart_idx');

        // キャンペーンコードを元に、キャンペーン適用
        if(self::TARGET_CAMPAIGN == $target)
        {
            //$post = Yii::$app->request->post('PurchaseForm',null);
            return $this->updateCampaign($cart_idx, Yii::$app->request);
        }

        // キャンペーンコードを元に、キャンペーン解除
        if(self::TARGET_CAMPAIGN_DEL == $target)
        {
            //$post = Yii::$app->request->post('PurchaseForm',null);
            return $this->unsetCampaign($cart_idx);
        }

        // サポート注文適用
        if(self::TARGET_AGENT == $target)
        {
            return $this->updateAgent($cart_idx, Yii::$app->request);
        }

        // サポート注文解除
        if(self::TARGET_AGENT_DEL == $target)
        {
            return $this->unsetAgent($cart_idx, Yii::$app->request);
        }


        if(self::TARGET_QTY   == $target)
            return $this->updateQty($cart_idx);

        if('addr' == $target)
        {
            if(Yii::$app->user->isGuest || $cart instanceof \common\components\cart\RecipeCart)
                return $this->updateAddr4Guest($cart_idx);

            return $this->updateAddr4Member($cart_idx);
        }

        if(self::TARGET_DATE  == $target)
            return $this->updateDateTime($cart_idx);

        if(self::TARGET_GIFT  == $target)
            return $this->updateGift($cart_idx);

        if(self::TARGET_PAYMENT == $target)
            return $this->updatePayment($cart_idx);

        if(self::TARGET_POINT == $target)
            return $this->updatePointConsume($cart_idx);

        if(self::TARGET_MSG   == $target)
            return $this->updateMsg($cart_idx, Yii::$app->request->queryParams);


        if(self::TARGET_COMPANION   == $target)
            return $this->updateCompanion($cart_idx, Yii::$app->request->queryParams);


        // ライブ配信チケット用 ゲスト情報
        if('guest-signup'   == $target)
            return $this->updateGuestSignup($cart_idx, Yii::$app->request->queryParams);


    }

    public function actionFinish($cart_idx=null)
    {
        if(null === $cart_idx)
            $cart_idx = Yii::$app->request->post('cart_idx', null);
        if(null === $cart_idx)
            throw new \yii\web\BadRequestHttpException('invalid post params');

        $purchase = $this->module->cart->getCart($cart_idx)->purchase;

        // 口座振替登録者のみ変更をかける
        if(!$this->module->cart->getCart($cart_idx)->purchase->agent_id) {
            if(Yii::$app->request->post('payment') && 1 < count($this->module->cart->getCart($cart_idx)->payments)) {
                if($this->module->updatePayment($cart_idx, Yii::$app->request->bodyParams)) {
                    Yii::$app->session->removeFlash('success');
                }
            }
        }
        
        $result = $this->module->finish($cart_idx);

        if($result && 'echom-frontend' == Yii::$app->id) {

            // ０円以上かつクレジット決済の時だけ決済ページへ行く
            if($purchase->total_charge > 0 && $purchase->payment->payment_id == \common\models\Payment::PKEY_CREDIT_CARD) {
//                Yii::$app->response->redirect('https://credit.homoeopathy.ac/credit.php?S_TORIHIKI_NO='.$purchase->purchase_id.'&OPT='.$purchase->email.'&AMOUNT='.$purchase->subtotal.'&TAX='.$purchase->tax.'&TOTAL='.$purchase->total_charge, 301)->send();
                $tax = floor($purchase->total_charge/11);
                $amount = $purchase->total_charge - $tax;
                Yii::$app->response->redirect('https://credit.homoeopathy.ac/credit.php?S_TORIHIKI_NO='.$purchase->purchase_id.'&OPT='.$purchase->email.'&AMOUNT='.$amount.'&TAX='.$tax.'&TOTAL='.$purchase->total_charge, 301)->send();
            } else {
                mkdir("/var/www/credit/www/live/".$purchase->purchase_id, 0755);
                symlink("/var/www/credit/www/lib/index.php", "/var/www/credit/www/live/".$purchase->purchase_id."/index.php");
                return $this->redirect(['complete', 'purchase' => $purchase->purchase_id]);
            }
        }
        return $this->redirect(['index','cart_idx'=>$cart_idx]);
    }

    /**
     * ライブ配信チケット専用クレジット決済リターン受付処理
     * credit_ret　　：決済結果（正常：ok,異常：ng）
     * errcode 　　　：エラーコード（処理に必要ありませんが一応渡しておきます）
     * purchase_id　： 注文番号
     * email 　　　　　：メールアドレス
     * watch_url 　　：視聴ページのurl 
     * 
     */
    public function actionResult($cart_idx=null)
    {
        $result = Yii::$app->request->get();
        if($result['credit_ret'] == 'ng') {
            $purchase = \common\models\Purchase::findOne($result['purchase_id']);
            // 決済エラー
            return $this->render('echom/credit_failed', [
                'purchase' => $purchase,
                'result'   => $result,
            ]);
            
        } else if ($result['credit_ret'] == 'ok') {
            $credit = true;
            if($purchase = \common\models\Purchase::findOne($result['purchase_id']))
            {
                $purchase->status = 7;//完了
                $purchase->email = $purchase->email ? $purchase->email : $result['email'];
                $purchase->save(false);
                
                if('echom-frontend' == Yii::$app->id) {
                    $mailer = new \common\components\sendmail\ECHomPurchaseMail(['model'=>$purchase]);
                } else {
                    $mailer = new \common\components\sendmail\PurchaseMail(['model'=>$purchase]);
                }
                
// var_dump($result);exit;
                if($mailer->thankyou($result)) {
                    return $this->redirect(['complete', 
                        'purchase' => $purchase->purchase_id,
                        'result'   => $result,
                    ]);
                } else {
                    return $this->redirect(['index','cart_idx'=>$cart_idx]);
                }
            }
        }
    }

    /**
     * チケット購入処理完了
     * StreamingBuyはここで生成する
     */
    public function actionComplete()
    {
        $get = Yii::$app->request->get();
        $purchase = \common\models\Purchase::findOne($get['purchase']);
        $result = isset($get['result']) ? $get['result'] : null;
        $credit = false;
	$sendmail = false;
        $companion_item = Yii::$app->session->get('companion_item');

        // 決済が完了したのでStreamingBuyを生成する
        foreach($purchase->items as $item) {
            // TODO: 有料イベント用処理 2023/04/18
            if(isset($companion_item)) {
                if($item->model->product_id == $companion_item && $purchase->status == \common\models\PurchaseStatus::PKEY_DONE) {
                    $this->updateSubscription($item);
                }
            }

            $streamings = \common\models\Streaming::find()->where(['product_id' => $item->model->product_id])->all();

            foreach($streamings as $streaming) {

                // StreamingBuyが重複するのを防止する必要？？　2020/04/27 kawai
                $record = \common\models\StreamingBuy::find()
                    ->where(['streaming_id' => $streaming->streaming_id])
                    ->andWhere(['create_date' => $purchase->create_date])
                    ->andWhere(['customer_id' => $purchase->customer_id])->one();
                
                if(!$record) {
		    $sendmail= true;

                    $streaming_buy = new \common\models\StreamingBuy();
                    $streaming_buy->streaming_id = $streaming->streaming_id;
                    $streaming_buy->customer_id = $purchase->customer_id;
                    $streaming_buy->create_date = $purchase->create_date;
                    $streaming_buy->update_date = $purchase->update_date;
                    $streaming_buy->expire_date = '2020-05-06 00:00:00';
                    // if()
                    if(!$streaming_buy->save(false)){
                        $transaction->rollBack();
                        throw new \yii\db\Exception('failed to save items');
                        return false;
                    }
                }
            }
        }


        if($purchase->total_charge == 0) {
            // actionResultをスルーしたのでステータス変更が必須
            if($purchase->payment->payment_id == \common\models\Payment::PKEY_CREDIT_CARD) {
                $credit = true;
                // var_dump($purchase->email);exit;
                $purchase->status = 7;
                $purchase->save(false);

                if($sendmail) {
                    if('echom-frontend' == Yii::$app->id) {
                        $mailer = new \common\components\sendmail\ECHomPurchaseMail(['model'=>$purchase]);
                    } else {
                        $mailer = new \common\components\sendmail\PurchaseMail(['model'=>$purchase]);
                    }
		    $mailer->thankyou($result);
                }

            }

            return $this->render('echom/complete_zero', [
                'purchase' => $purchase,
            ]);
        } else {
            return $this->render('echom/complete', [
                'purchase' => $purchase,
                'result'   => $result,

            ]);
        }        
    }

    /**
     * 有料イベント購入・決済完了時に申し込み人数を更新する
     */
    public function updateSubscription($item) {

        if(Yii::$app->session->get('companion_subscription')) {
            $subscription = Yii::$app->session->get('companion_subscription');
            $liveItemInfo = \common\models\LiveItemInfo::find()->where(['product_id'=>$item->product_id])->one();
            $liveInfo = $liveItemInfo ? $liveItemInfo->info : null;
            if($liveInfo->companion) {
                $liveInfo->subscription = $liveInfo->subscription + $subscription;
                
                if(!$liveInfo->save()){
                    Yii::error($liveInfo->errors);
                    throw new \yii\db\Exception("申込み人数更新時にエラーが発生しました。処理を中止します");
                    return false;
                }

                if($liveInfo->capacity == $liveInfo->subscription) {
                    $product = \common\models\Product::find()->where(['product_id' => $item->product_id])->one();
                    $product->restrict_id = 99; // 非公開にセットする
                    if(!$product->save()) {
                        Yii::error($product->errors);

                        throw new \yii\db\Exception("イベント情報更新時にエラーが発生しました。処理を中止します");
                        return false;
                    }                        
                }

                // 役目を終えたのでセッション情報を掃除
                Yii::$app->session->remove('companion');
                Yii::$app->session->remove('companion_info_id');
                Yii::$app->session->remove('companion_data');
                Yii::$app->session->remove('companion_price');
                Yii::$app->session->remove('companion_tax');
                Yii::$app->session->remove('companion_item');
                Yii::$app->session->remove('companion_subscription');
                Yii::$app->session->remove('live_notes_companion');

            }
        }
    }


    /**
     * サポート注文時の同意確認画面
     * @param type $cart_idx
     * @return type
     */
    public function actionAgreement($cart_idx=null) {
        $carts = $this->module->cart->activeCarts;
        return $this->render('agreement', [
                'customer' => $this->module->customer,
                'carts'    => $carts,
                'cart_idx' => $cart_idx,
            ]);
    }

    /**
     * 相談チケット購入時確認画面
     * @param type $pid
     * @return type
     */
    public function actionTextAgreement($pid) {
        return $this->render('text-agreement', [
                'pid'  => $pid
            ]);
    }

    /* @return void */
    private function applyPaymentFilter()
    {
        $cart_idx = Yii::$app->request->get('cart_idx', 0);
        $carts    = array_keys($this->module->cart->activeCarts);

        if(! in_array($cart_idx, array_keys($carts)))
            return; // invalid cart_idx
        else
            $cart_idx = (int) $cart_idx;

        if(! $customer = $this->module->customer)
            return; // guest user

        if(CustomerGrade::PKEY_KA < $customer->grade_id)
            return; // スペシャルより偉い

        if(! isset($customer->ysdAccount) || !$customer->ysdAccount->isValid()) { 
            return; // 口座振替なし

        }

        if(CustomerGrade::PKEY_AA == $customer->grade_id)
            $msg = 'スタンダード会員の場合、ご登録いただいた口座振替はご利用できません。';


    }

     /**
     * カートに商品を入れた後ログインした場合等を考慮
     * 
     */
    private function checkDiscount()
    {
        $cart_idx = Yii::$app->request->get('cart_idx', 0);
        $carts    = array_keys($this->module->cart->activeCarts);

        if(! in_array($cart_idx, array_keys($carts))) {
            return; // invalid cart_idx
        } else {
            $cart_idx = (int) $cart_idx;
        }

        $cart = $this->module->cart->activeCarts[$carts[$cart_idx]];
        $purchase = $cart->purchase;
        $notes = $purchase->note;

        if(strpos($notes, "一括購入値引き適用") !== false) {
            $discount = 0;
            if(count($cart->items) < 3) {
                $note = str_replace("一括購入値引き適用", "",$purchase->note);
                $purchase->note = $note;
            } else {
                // 一括購入値引き
                $discount += $this->getLumpSumDiscount($cart->customer);
                if(strpos($notes, "クーポン") !== false) {
                    // クーポン値引き５００円
                    $discount += 500;
                }
    
            }


            $purchase->discount = $discount;
    
        }
        $purchase->compute();
    }

    /**
     * 一括購入用 顧客ランクに応じた値引き
     * 一般：1500円値引き
     * スタンダード、スペシャル： 1000円値引き
     * スペシャルプラス、プレミアム、プレミアムプラス：500円値引き
     */
    private function getLumpSumDiscount($customer=null)
    {
        $discount = 1500;
        if($customer && $customer->customer_id) {
            if($customer->isAgencyOf(\common\models\Company::PKEY_HE))
                return 500; // HE代理店はプレミアムプラス扱い 2021/05/13

            switch($customer->grade_id) {
                /*
                const PKEY_AA = 1;
                const PKEY_KA = 2;
                const PKEY_SA = 3;
                const PKEY_TA = 4;
                const PKEY_NA = 5;
                */
                case \common\models\CustomerGrade::PKEY_AA:
                case \common\models\CustomerGrade::PKEY_KA:
                    $discount = 1000;
                    break;
                case \common\models\CustomerGrade::PKEY_SA:
                case \common\models\CustomerGrade::PKEY_TA:
                case \common\models\CustomerGrade::PKEY_NA:
                    $discount = 500;
                    break;
                default:
            
            }
        }
        return $discount;
    }
    

    private function updateAddr4Guest($cart_idx)
    {
        $model = new \common\models\AddrbookForm();
        if($scenario = Yii::$app->request->post('scenario'))
            $model->scenario = $scenario;

        $params = $this->module->cart->getCart($cart_idx)->delivery->attributes;
        $model->load($params,'');

        if($model->load(Yii::$app->request->bodyParams))
        {
            if($model::SCENARIO_ZIP2ADDR == $model->scenario)
                $model->zip2addr();

            elseif($this->module->updateAddr($cart_idx, $model))
                return $this->redirect(['index','cart_idx'=>$cart_idx]);
        }


        $update = 'update';
        if('echom-frontend' == Yii::$app->id)
            $update = 'echom/update';


        return $this->render($update, [
            'target'    => self::TARGET_ADDRESS,
            'cart_idx'  => $cart_idx,
            'model'     => $model,
        ]);
    }

    private function updateGuestSignup($cart_idx)
    {
        return $this->redirect(['guest/signup']);
    }

    private function updateAddr4Member($cart_idx)
    {
        if(($id = Yii::$app->request->get('id')) !== null)
        {
            if(0 == $id)
                $model = Yii::$app->user->identity;
            else
                $model = \common\models\CustomerAddrbook::findOne([
                    'customer_id' => Yii::$app->user->id,
                    'id'          => $id,
                ]);
            if(! isset($model))
                throw new \yii\base\UserException();

            if($this->module->updateAddr($cart_idx, $model))
                return $this->redirect(['index','cart_idx'=>$cart_idx]);
        }
        if(! isset($model))
        {
            $model = new \common\models\CustomerAddrbook();
            $model->load($this->module->cart->getCart($cart_idx)->delivery->attributes,'');
        }

        $update = 'update';
        if('echom-frontend' == Yii::$app->id)
            $update = 'echom/update';

        return $this->render($update, [
            'target'    => self::TARGET_ADDRBOOK,
            'cart_idx'  => $cart_idx,
            'model'     => $model,
        ]);
    }

    private function updateDateTime($cart_idx)
    {
        if($this->module->cart->getCart($cart_idx)->company->company_id == \common\models\Company::PKEY_TY) {
            $items = $this->module->cart->getCart($cart_idx)->items;
            if(count($items) > 0 && $items[0]->getModel()->product_id == \common\models\Product::PKEY_OSECHI) {
                $deliv_date = "2017-12-28 18:00:00";
                $model = new \common\models\DeliveryDateTimeForm(['company_id'=>$cart_idx, 'osechi_date' => strtotime($deliv_date)]);
                $model->osechi_date = strtotime($deliv_date);
            } else {
                $model = new \common\models\DeliveryDateTimeForm(['company_id'=>$cart_idx]);
            }

        } else {
            $model = new \common\models\DeliveryDateTimeForm(['company_id'=>$cart_idx]);
        }
        $params = [
            $model->formName() => $this->module->cart->getCart($cart_idx)->delivery->attributes
        ];
        $model->load($params);

        $ret = false;

        if(Yii::$app->request->isPost &&
           $model->load(Yii::$app->request->bodyParams) &&
           $model->validate())
        {
            $ret = $this->module->updateDateTime($cart_idx, $model->date, $model->time_id);
        }
        if($ret)
            return $this->redirect(['index','cart_idx'=>$cart_idx]);

        $update = 'update';
        if('echom-frontend' == Yii::$app->id)
            $update = 'echom/update';
    
        return $this->render($update, [
            'target'   => self::TARGET_DATE,
            'model'    => $model,
            'cart'     => $this->module->cart->getCart($cart_idx),
            'cart_idx' => $cart_idx,
        ]);
    }

    private function updateGift($cart_idx)
    {
        if(Yii::$app->user->isGuest)
            throw new \yii\base\UserException('Guest is not allowed request this page');

        if(null !== ($gift = Yii::$app->request->post('gift')))
        {
            $this->module->updateGift($cart_idx, ['gift'=> $gift]);
            return $this->redirect(['index','cart_idx'=>$cart_idx]);
        }

        $update = 'update';
        if('echom-frontend' == Yii::$app->id)
            $update = 'echom/update';

        return $this->render($update,[
            'target'  => 'gift',
            'cart_idx'=> $cart_idx,
            'cart'    => $this->module->cart->getCart($cart_idx),
        ]);
    }

    private function updateMsg($cart_idx)
    {
        $this->module->updateMsg($cart_idx, Yii::$app->request->bodyParams);
        $this->redirect(['index','cart_idx'=>$cart_idx]);
    }

    private function updateCompanion($cart_idx)
    {

        if(Yii::$app->request->isPost) {
            $companion = Yii::$app->request->bodyParams['companion'];
            if($companion) {
                $liveInfo = LiveInfo::findOne(Yii::$app->session->get('companion_info_id'));
                $capacity = $liveInfo->capacity;
                $left = $capacity - $liveInfo->subscription;
    
                $companion = json_decode($companion, true);
                $companion_text = "";
                $companion_type = "";
                $companion_price = 0;
                $companion_tax = 0;
                $companion_subscription = count($companion);
                if($companion_subscription > $left) {
                    $over = $companion_subscription - $left;
                    Yii::$app->getSession()->addFlash('error',"申込人数が定員を".$over."人分オーバーしています<br/>");
                    $this->redirect($this->_backUrl);
                    return false;
                }

                for($i=0; $i<count($companion); $i++) {
                    $companion_type = $companion[$i][0];
                    $companion_text .= $companion_type == "本人" ? "大人・" : $companion_type."・";
                    $companion_price += $companion[$i][1];
                    $companion_tax += $companion[$i][4];
                    $companion_text .= $companion[$i][2];
                    $companion_text .= $companion[$i][3];
                    if($i+1 >= 1 && $i < count($companion)-1)
                        $companion_text .= ' ';
                }
                $companion_price = $companion_price - $companion_tax;
                            // イベント同行者 2023/04/12
                $param['companion_info_id'] = Yii::$app->session->get('companion_info_id');
                $param['companion_data'] = $companion ? $companion : null;
                $param['companion'] = $companion_text ? $companion_text : null;
                $param['companion_item'] = Yii::$app->session->get('companion_item');
                $param['companion_price'] = $companion_price ? $companion_price : null; 
                $param['companion_tax'] = $companion_tax ? $companion_tax : null;
                $param['capacity'] = $capacity ? $capacity : null;
                $param['companion_subscription'] = $companion_subscription ? $companion_subscription : null;
                $param['PurchaseForm']['note'] = "";
                Yii::$app->session['companion_data'] = $companion ? $companion : null;
                Yii::$app->session['companion'] = $companion_text ? $companion_text : null;
                Yii::$app->session['companion_price'] = $companion_price ? $companion_price : null; 
                Yii::$app->session['companion_tax'] = $companion_tax ? $companion_tax : null; 
                Yii::$app->session['companion_subscription'] = $companion_subscription ? $companion_subscription : null; 

            }
    
            if($this->module->updateCompanion($cart_idx, $param))
                return $this->redirect(['index','cart_idx'=>$cart_idx]);
        }

        $cart     = $this->module->cart->getCart($cart_idx);
        
        $liveItemInfo = \common\models\LiveItemInfo::find()->where(['product_id'=> Yii::$app->session->get('companion_item')])->one();
        $model = $liveItemInfo ? $liveItemInfo->info : null;
                
        $update = 'update';
        if('echom-frontend' == Yii::$app->id)
            $update = 'echom/update';

        return $this->render($update, [
            'target'   => self::TARGET_COMPANION,
            'model'    => $model,
            'cart'     => $cart,
            'cart_idx' => $cart_idx,
        ]);
                
    }

    private function updatePayment($cart_idx)
    {
        if(Yii::$app->request->isPost)
            if($this->module->updatePayment($cart_idx, Yii::$app->request->bodyParams))
                return $this->redirect(['index','cart_idx'=>$cart_idx]);

        $cart     = $this->module->cart->getCart($cart_idx);
        $payments = $cart->payments;

        $update = 'update';
        if('echom-frontend' == Yii::$app->id)
            $update = 'echom/update';

        return $this->render($update, [
            'target'   => self::TARGET_PAYMENT,
            'model'    => $payments,
            'cart'     => $cart,
            'cart_idx' => $cart_idx,
        ]);
    }

    private function updatePointConsume($cart_idx)
    {
        $this->module->updatePointConsume($cart_idx, Yii::$app->request->bodyParams);
        $this->redirect(['index','cart_idx'=>$cart_idx]);
    }

    private function updateQty($cart_idx)
    {
        $request = Yii::$app->request;

        $this->module->updateQty($cart_idx, $request->isPost? $request->bodyParams : $request->queryParams);

        $support_entry = false;
        $cart = $this->module->cart->getCart($cart_idx);
        $items = $cart->items;
        $purchase = $cart->getPurchase();
        foreach($items as $item)
        {
            $liveItemInfo = \common\models\LiveItemInfo::find()->where(['product_id'=>$item->model->product_id])->one();
            $liveInfo = $liveItemInfo ? $liveItemInfo->info : null;
            // カートにサポート申込対応チケットが入っている
            if(isset($liveInfo) && $liveInfo->support_entry) {
                $support_entry = $liveInfo->support_entry;
                break;
            }
        }
        if($purchase->agent_id && !$support_entry)
            $this->module->unsetAgent($cart_idx, "");

        $this->redirect(['index','cart_idx'=>$cart_idx]);
    }

    private function updateCampaign($cart_idx, $request)
    {

        $this->module->updateCampaign($cart_idx, $request->isPost? $request->bodyParams : $request->queryParams);
        $this->redirect(['index','cart_idx'=>$cart_idx]);
    }

    private function unsetCampaign($cart_idx)
    {
        $request = Yii::$app->request;

//        $this->module->deleteCampaign($cart_idx, $request->isPost? $request->bodyParams : $request->queryParams);
        $this->module->unsetCampaign($cart_idx);

        $this->redirect(['index','cart_idx'=>$cart_idx]);
    }
    
    
    private function updateAgent($cart_idx, $request)
    {

        $this->module->updateAgent($cart_idx, $request->isPost? $request->bodyParams : $request->queryParams);
        $this->redirect(['index','cart_idx'=>$cart_idx]);
    }

    private function unsetAgent($cart_idx, $request)
    {
//        $request = Yii::$app->request;

        $this->module->unsetAgent($cart_idx,$request->isPost? $request->bodyParams : $request->queryParams);

        $this->redirect(['index','cart_idx'=>$cart_idx]);
    }

}
