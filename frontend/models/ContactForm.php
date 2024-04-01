<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/models/ContactForm.php $
 * $Id: ContactForm.php 3370 2017-06-02 05:49:40Z naito $
 */

class ContactForm extends Model
{
    public $customer_id;
    public $name;
    public $email;
    public $subject;
    public $body;
    public $verifyCode;

    public $subjects = [
        0 => 'ご注文について',
        1 => '会員情報の修正',
        2 => 'その他',
    ];

    public function init()
    {
        parent::init();
        if(! Yii::$app->user->isGuest)
        {
            $customer = Yii::$app->user->identity;
            $this->name  = $customer->name;
            $this->email = $customer->email;
            $this->customer_id = $customer->customer_id;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'email', 'subject', 'body'], 'trim'],
            ['customer_id', 'exist', 'targetClass'=>\common\models\Customer::className(), 'targetAttribute'=>'customer_id'],
            [['name', 'email', 'subject', 'body'], 'required'],
            ['email', 'email'],
            ['subject','in','range'=>[0,1,2]],
            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name'       => "お名前",
            'email'      => "メールアドレス",
            'subject'    => "お問合せの区分",
            'body'       => "本文",
            'verifyCode' => "画像認証「表示されている文字を入力してください」（空白は不要です）",
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @param  string  $email the target email address
     * @return boolean whether the email was sent
     */
    public function sendEmail()
    {
        return \common\components\sendmail\ContactMail::thankyou($this);
    }
}
