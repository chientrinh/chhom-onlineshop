<?php
namespace common\components\sendmail;

use Yii;
use \common\models\Branch;
use \common\models\Company;

class SodanPurchaseMail extends \yii\base\Component
{
    /* Purchase model */
    public $model;
    public $result;

    public function validate()
    {
        $model = $this->model;

        if(! $model = $this->model)
        {
            Yii::error("no model is defined", self::className().'::'.__FUNCTION__);
            return false;
        }

        if('echom-frontend' !== Yii::$app->id)
        {
            Yii::warning("this function is for ライブ配信チケット shopping only", self::className().'::'.__FUNCTION__);
            return false;
        }

        if(! $model->delivery)
        {
            Yii::warning("delivery is not set, something is wrong", self::className().'::'.__FUNCTION__);
            return false;
        }

        if(! $email = (($c = $model->customer) && $c->email) ? $c->email : $model->email)
        {
            Yii::warning("email not defined in purchase: ".$model->purchase_id,
                         self::className().'::'.__FUNCTION__);
            return false;
        }

        if(! $from = $this->getFromAddr())
        {
            Yii::error("could not detect sender for purchase: ".$model->purchase_id,
                         self::className().'::'.__FUNCTION__);
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function thankyou($result = null)
    {
        $model   = $this->model;

        $subject = sprintf("%s ご注文の確認 [%06d]", Yii::$app->name, $model->purchase_id);
        $sender  = $this->getFromAddr();
        $customer= $this->getCustomer();

        $mailer = Yii::$app->mailer;
        $mailer->table = $model->tableName();
        $mailer->pkey  = $model->purchase_id;

        if(! $this->validate()) {
            $sender_msg = $mailer->compose(
                [
                    'text'  => 'thankyou-sodan-purchase-text'
                ],
                [
                    'customer' => $customer,
                    'model'    => $model,
                    'result'   => $result,
                    'sender'   => array_shift(array_keys($sender)),
                ])
                    ->setTo($sender)
                    ->setFrom($sender)
                    ->setSubject($subject);
            // センターには必ずメールを送る
            $sender_msg->send();
            return false;
        }        

        $email   = [];
        if($model->email)                       { array_push($email, $model->email); }
        if(($c = $model->customer) && $c->email){ array_push($email, $c->email);     }
        $email   = array_combine($email, $email);       

        $msg = $mailer->compose(
            [
                'text'  =>  'thankyou-sodan-purchase-text'
            ],
            [
                'customer' => $customer,
                'model'    => $model,
                'result'   => $result,
                'sender'   => array_shift(array_keys($sender)),
            ])
                ->setTo($email)
                ->setFrom($sender)
                ->setBcc($sender)
                ->setSubject($subject);

        $send = false;
        if($msg->send())
            $send = true;

        // サポート申込時に代理注文者の情報を取得し、タイトルを変えて再送信
        if($model->agent_id && ($cc = $this->getAgentCustomer())) {
            $msg = $mailer->compose(
            [
                'text'  => 'thankyou-sodan-purchase-text'
            ],
            [
                'customer' => $customer,
                'model'    => $model,
                'result'   => $result,
                'sender'   => array_shift(array_keys($sender)),
            ])
                ->setTo($cc->email)
                ->setFrom($sender)
                ->setSubject($subject . ' 【サポート申込】');

            if ($msg->send())
                $send = true;
        }

        /**
         * キャンペーンが設定されたチケットの購入の場合、もう一通追加送信する
         */
        $items = $model->items;
        foreach($items as $item) {
            $liveItemInfo = \common\models\LiveItemInfo::find()->where(['product_id'=>$item->product_id])->one();
            $liveInfo = $liveItemInfo ? $liveItemInfo->info : null;
            if(isset($liveInfo)) {
                if($liveInfo->pre_order_code) {
                    $pre_order_code = $liveInfo->pre_order_code;
                    $campaign  = \common\models\Campaign::find()->where(['campaign_code' => $pre_order_code])->active()->one();
                    if($campaign) {
                        // チケット予約詳細を確認し「Product_id・」を含み、「Product_id・自宅」ではない
                        $note = $model->note;
                        // error_log($note."\n",3,"/var/www/mall/mall_dev_log");
                        if(false !== strpos($note,$item->product_id."・") && false === strpos($note,$item->product_id."・自宅") && $model->customer_id) {
                            $pre_order_period = $liveInfo->pre_order_period;
                            // 事前注文受付メールを送信する
                            $subject = "【コングレス会場参加者限定】事前注文受付のお知らせ";
                            $email   = [];
                            if($model->email)                       { array_push($email, $model->email); }
                            if(($c = $model->customer) && $c->email){ array_push($email, $c->email);     }
                            $email   = array_combine($email, $email);       
                    
                            $msg = $mailer->compose(
                                [
                                    'text'  => 'thankyou-echom-pre_order-text'
                                ],
                                [
                                    'pre_order_code' => $pre_order_code,
                                    'pre_order_period'   => $pre_order_period,
                                    'sender'   => array_shift(array_keys($sender)),
                                ])
                                    ->setTo($email)
                                    ->setFrom($sender)
                                    ->setBcc($sender)
                                    ->setSubject($subject);
                    
                            $send = false;
                            if($msg->send())
                                $send = true;
                    
                            // error_log("キャンペーンメールの送信処理完了：\n",3,"/var/www/mall/mall_dev_log");
                        } else {
                            // error_log($item->product_id."\n",3,"/var/www/mall/mall_dev_log");
                        }
                    }
                }
                if($liveInfo->campaign_code) {      
                    $campaign_code = $liveInfo->campaign_code;
                    $campaign_type    = $liveInfo->campaign_type;
                    $campaign_period = $liveInfo->campaign_period;
                    // キャンペーンコード確認メールを送信する
                    $subject = "【イベント参加者限定】豊受モールお買い物ポイントアップキャンペーンのお知らせ";
                    $email   = [];
                    if($model->email)                       { array_push($email, $model->email); }
                    if(($c = $model->customer) && $c->email){ array_push($email, $c->email);     }
                    $email   = array_combine($email, $email);       
            
                    $msg = $mailer->compose(
                        [
                            'text'  => 'thankyou-echom-campaign-text'
                        ],
                        [
                            'campaign_code' => $campaign_code,
                            'campaign_type'    => $campaign_type,
                            'campaign_period'   => $campaign_period,
                            'sender'   => array_shift(array_keys($sender)),
                        ])
                            ->setTo($email)
                            ->setFrom($sender)
                            ->setBcc($sender)
                            ->setSubject($subject);
            
                    $send = false;
                    if($msg->send())
                        $send = true;
            
                    // error_log("キャンペーンメールの送信処理完了：\n",3,"/var/wmas0602KWww/mall/mall_dev_log");
                    // break;
                }
            }
        }

        if (!$send) {
            Yii::error("mailer failed for purchase: ".$model->purchase_id);
            return false;
        }
        return true;
    }

    public function ticket($ticket_id)
    {
        if(! $this->validate())
            return false;

        $model   = $this->model;
        $subject = sprintf("%s 相談会チケット番号のお知らせ", Yii::$app->name);
        $sender  = $this->getFromAddr();
        $customer= $this->getCustomer();

        $email   = [];
        if($model->email)                       { array_push($email, $model->email); }
        if(($c = $model->customer) && $c->email){ array_push($email, $c->email);     }
        $email   = array_combine($email, $email);

        $mailer = Yii::$app->mailer;
        $mailer->table = $model->tableName();
        $mailer->pkey  = $model->purchase_id;

        $msg = $mailer->compose(
            [
                'text'  => 'ticket-purchase-text'
            ],
            [
                'customer' => $customer,
                'model'    => $model,
                'sender'   => array_shift(array_keys($sender)),
                'ticket_id'=> $ticket_id
            ])
                ->setTo($email)
                ->setFrom($sender)
                ->setBcc($sender)
                ->setSubject($subject);

        // サポート注文時に代理注文者の情報を取得し、CCにセットする
        if($model->agent_id && ($cc = $this->getAgentCustomer()))
            $msg->setCc($cc->email);

        if($msg->send())
            return true;

        Yii::error("mailer failed for purchase: ".$model->purchase_id);
        return false;
    }

    public function canceled()
    {
        if(! $this->validate())
            return false;

        $model   = $this->model;
        $subject = sprintf("CHhomオンラインショップ ご注文のキャンセル [%06d]", $model->purchase_id);
        $email   = (($c = $model->customer) && $c->email) ? $c->email : $model->email;
        $from    = $this->getFromAddr();
        $bcc     = $this->getBccAddr();
        $customer= $this->getCustomer();

        $mailer = Yii::$app->mailer;
        $mailer->table = $model->tableName();
        $mailer->pkey  = $model->purchase_id;

        $msg = $mailer->compose(
            [
                'text'  => 'canceled-purchase-text'
            ],
            [
                'customer' => $customer,
                'model'    => $model,
                'sender'   => array_shift(array_keys($sender)),
            ])
                ->setTo($email)
                ->setFrom($sender)
                ->setBcc($bcc)
                ->setSubject($subject);

        if($msg->send())
            return true;

        Yii::error("mailer failed for purchase: ".$model->purchase_id);
        return false;
    }

    private function getBccAddr()
    {
        $bcc = $this->getFromAddr();
        if(($user = Yii::$app->user->identity) && $user instanceof \backend\models\Staff)
            $bcc[] = $user->email;

        return $bcc;
    }

    private function getCustomer()
    {
        $model = $this->model;

        if(! $customer = $model->customer)
        {
            $customer = new \common\models\Customer();
            $customer->load($model->delivery->attributes,'');
        }

        return $customer;
    }
    
    /**
     * サポート注文時に代理注文者の情報を取得
     * @return \common\models\Customer
     */
    private function getAgentCustomer()
    {
        $model = $this->model;

        if(! $customer = \common\models\Customer::findOne(['customer_id' => $model->agent_id]))
        {
            return null;
        }

        return $customer;
    }

    private function getFromAddr()
    {
        return ['center@homoeopathy.ac' => Yii::$app->name ];
    }
}
