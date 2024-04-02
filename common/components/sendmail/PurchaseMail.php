<?php
namespace common\components\sendmail;

use Yii;
use \common\models\Branch;
use \common\models\Company;

class PurchaseMail extends \yii\base\Component
{
    /* Purchase model */
    public $model;

    public function validate()
    {
        if(! $model = $this->model)
        {
            Yii::error("no model is defined", self::className().'::'.__FUNCTION__);
            return false;
        }

        if('app-frontend' !== Yii::$app->id)
        {
            Yii::warning("this function is for web shopping only", self::className().'::'.__FUNCTION__);
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
    public function thankyou()
    {
        if(! $this->validate())
            return false;

        $model   = $this->model;
        $subject = sprintf("%s ご注文の確認 [%06d]", Yii::$app->name, $model->purchase_id);
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
                'text'  => 'thankyou-purchase-text'
            ],
            [
                'customer' => $customer,
                'model'    => $model,
                'sender'   => array_shift(array_keys($sender)),
            ])
                ->setTo($email)
                ->setFrom($sender)
                ->setBcc($sender)
                ->setSubject($subject);

        $send = false;
        if($msg->send())
            $send = true;

        // サポート注文時に代理注文者の情報を取得し、タイトルを変えて再送信
        if($model->agent_id && ($cc = $this->getAgentCustomer())) {
            $msg = $mailer->compose(
            [
                'text'  => 'thankyou-purchase-text'
            ],
            [
                'customer' => $customer,
                'model'    => $model,
                'sender'   => array_shift(array_keys($sender)),
            ])
                ->setTo($cc->email)
                ->setFrom($sender)
                ->setBcc($sender)
                ->setSubject($subject . ' 【サポート注文】');

            if ($msg->send())
                $send = true;
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
        $subject = sprintf("%s 相談会チケット番号のお知らせ [%06d]", Yii::$app->name, $model->purchase_id);
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
        $subject = sprintf("豊受モール ご注文のキャンセル [%06d]", $model->purchase_id);
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
        $branch = $this->model->branch;

        if(! $branch){ return null; }

        return [$branch->email => sprintf('%s (%s)', Yii::$app->name, $branch->name) ];
    }
}
