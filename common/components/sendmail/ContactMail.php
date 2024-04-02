<?php
namespace common\components\sendmail;

use Yii;

class ContactMail extends \yii\base\Component
{
    public static function thankyou($form)
    {
        if($form->hasErrors())
        {
            Yii::warning("model is not valid, stop send email.", self::className().'::'.__FUNCTION__);
            return false;
        }

        $subject = sprintf("%s お問合せの確認", Yii::$app->name);
        $from    = [Yii::$app->params['supportEmail'] => Yii::$app->name ];
        $bcc     = Yii::$app->params['supportEmail'];

        $mailer = \Yii::$app->mailer;
        $mailer->table = ($form->customer_id) ? \common\models\Customer::tableName() : null;
        $mailer->pkey  = ($form->customer_id) ? $form->customer_id : null;

        $msg = $mailer->compose(
            [
                'text'  => 'thankyou-contact-text'
            ],
            [
                'form'  => $form,
            ])
             ->setTo($form->email)
             ->setFrom($from)
             ->setBcc($bcc)
             ->setSubject($subject);

        if(! $msg->send())
            return false;

        return true;
    }
}