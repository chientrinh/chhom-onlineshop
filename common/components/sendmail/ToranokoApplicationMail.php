<?php
namespace common\components\sendmail;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/sendmail/ToranokoApplicationMail.php $
 * $Id: ToranokoApplicationMail.php 3096 2016-11-20 02:51:44Z mori $
 */

use Yii;
use \backend\models\Staff;
use \common\models\Branch;

class ToranokoApplicationMail extends \yii\base\Component
{
    public $branch;   /* Branch model who in charge of this Transaction */
    public $customer; /* Customer model who becomes Toranoko member */
    public $agency;   /* Customer model by which Agency has been sold */
    public $product;  /* Product model for what membership applied */
    public $invoiceC; /* (Purchase | Pointing) of what Customer paid */
    public $invoiceA; /* Pointing model of what Agency earned */
    public $paid;     /* bool whether Customer has paid the admission */

    private $_from;

    public function init()
    {
        parent::init();

        $this->_from = Branch::findOne(Branch::PKEY_HE_TORANOKO)->email;
    }
    
    public function sendMail()
    {
        $ret   = [];
        $ret[] = $this->welcomeCustomer();
        $ret[] = $this->thankToCustomer();
        $ret[] = $this->thankToAgency();

        foreach($ret as $r)
            if(! $r) return false;

        return true;
    }

    public function welcomeCustomer()
    {
        $subject = "とらのこ会へようこそ";

        if($this->customer->isToranoko())
            return true;

        if(! $this->customer->email || ! $this->customer->validate(['email']))
            return true;

        $mailer = Yii::$app->mailer;
        $message= $mailer->compose(
            ['text' => 'welcome-to-toranoko-text'],
            [
                'customer' => $this->customer,
                'branch'   => $this->branch,
                'paid'     => $this->paid,
            ]);
        $message->setSubject($subject)
                ->setTo($this->customer->email)
                ->setFrom($this->_from);

        if(Yii::$app->get('user') && ($staff = Yii::$app->user->identity) && $staff instanceof Staff)
            $message->setBcc($staff->email);

        return $message->send();
    }

    public function thankToCustomer()
    {
        $subject = "とらのこ会 年会費のご案内";

        if(! $this->customer->email || ! $this->customer->validate(['email']) || $this->paid )
            return true;

        $mailer = Yii::$app->mailer;
        $message= $mailer->compose(
            ['text' => 'thank-to-toranoko-customer-text'],
            [
                'invoice'  => $this->invoiceC,
                'customer' => $this->customer,
                'branch'   => $this->branch,
            ]);
        $message->setSubject($subject)
                ->setTo($this->customer->email)
                ->setFrom($this->_from);

        $mailer->table = $this->invoiceC->tableName();
        $mailer->pkey  = ! is_array($this->invoiceC->primaryKey) ? $this->invoiceC->primaryKey : null;

        if(Yii::$app->get('user') && ($staff = Yii::$app->user->identity) && $staff instanceof Staff)
            $message->setBcc($staff->email);

        return $message->send();
    }

    public function thankToAgency()
    {
        $subject = "とらのこ会 年会費 手続きのご確認";

        if(! $this->agency)
            return true;

        $mailer = Yii::$app->mailer;
        $message= $mailer->compose(
            ['text' => 'thank-to-toranoko-agency-text'],
            [
                'invoice' => $this->invoiceA,
                'agency'  => $this->agency,
                'branch'  => $this->branch,
            ]);
        $message->setSubject($subject)
                ->setTo($this->agency->email)
                ->setFrom($this->_from);

        $mailer->table = $this->invoiceA->tableName();
        $mailer->pkey  = ! is_array($this->invoiceA->primaryKey) ? $this->invoiceA->primaryKey : null;

        if(Yii::$app->get('user') && ($staff = Yii::$app->user->identity) && $staff instanceof Staff)
            $message->setBcc($staff->email);

        return $message->send();
    }
}
