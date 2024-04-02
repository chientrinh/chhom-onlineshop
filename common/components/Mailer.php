<?php

namespace common\components;
use Yii;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/Mailer.php $
 * $Id: Mailer.php 2823 2016-08-10 02:41:00Z mori $
 */

class Mailer extends \yii\swiftmailer\Mailer
{
    public $table;
    public $pkey;

    public function afterSend($msg, $isSuccessful)
    {
        $logger = new \common\models\MailLog([
            'to'      => implode(',', array_keys($msg->to)),
            'sender'  => implode(',', array_keys($msg->from)),
            'subject' => $msg->subject,
            'body'    => self::extractMessageBody($msg),
            'tbl'     => $this->table ? $this->table : null,
            'pkey'    => $this->pkey  ? $this->pkey  : null,
        ]);
        $this->table = null;
        $this->pkey  = null; // set null to prevend reuse of these keys

        if(! $isSuccessful)
            Yii::error("メール送信が失敗しました。");
        if(! $logger->save())
            Yii::error("配信ログの保存に失敗しました: " . json_encode($logger->errors));
    }

    private static function extractMessageBody($msg)
    {
        $lines = explode("\r\n\r\n", quoted_printable_decode($msg->toString()));
        array_shift($lines);

        return implode("\r\n\r\n", $lines);
    }

}
