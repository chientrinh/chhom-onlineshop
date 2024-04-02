<?php
namespace common\components\sendmail;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/sendmail/MailForm.php $
 * $Id: MailForm.php 3085 2016-11-17 06:22:47Z mori $
 */

use Yii;

class MailForm extends \yii\base\Model
{
    public $recipient;
    public $sender;
    public $subject;
    public $content;

    public $table; // ltb_mailer.talbe
    public $pkey;  // ltb_mailer.pkey

    /**
     * @return bool
     */
    public function rules()
    {
        return [
            ['sender','default','value'=> ($u = Yii::$app->user->identity) ? $u->email : null],
            [['subject','recipient','sender','content'], 'required'],
            [['subject'], 'string', 'min' =>  8, 'max' =>  255],
            [['content'], 'string', 'min' => 16, 'max' => 2048],
            [['recipient','sender'], 'email'],
            [['table'], 'string' ],
            [['pkey'],  'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'sender'    => '差出人',
            'recipient' => '宛先',
            'subject'   => '件名',
            'content'   => '本文',
        ];
    }

    public function send()
    {
        if(! $this->validate())
            return false;

        $mailer = Yii::$app->mailer;
        $mailer->table = $this->table;
        $mailer->pkey  = $this->pkey;

        return $mailer->compose()
                      ->setTo($this->recipient)
                      ->setFrom($this->sender)
                      ->setBcc($this->sender)
                      ->setSubject($this->subject)
                      ->setTextBody($this->content)
                      ->send();
    }

}
