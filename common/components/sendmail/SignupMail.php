<?php
namespace common\components\sendmail;

use Yii;

class SignupMail extends \yii\base\Component
{
    public static function thankyou($customer)
    {
        if($customer->isNewRecord)
        {
            Yii::warning("model is not inserted, stop send email.", self::className().'::'.__FUNCTION__);
            return false;
        }

        $subject = "【豊受オーガニクスモール】会員登録完了のお知らせ"; 
        //sprintf("%s ご登録の確認", Yii::$app->name); // 件名はここを変更する
        $from    = [Yii::$app->params['supportEmail'] => Yii::$app->name ];
        $bcc     = Yii::$app->params['supportEmail'];

        $mailer = \Yii::$app->mailer;
        $mailer->table = $customer->tableName();
        $mailer->pkey  = $customer->customer_id;

        $msg = $mailer->compose(
            [
                'text'  => 'thankyou-signup-text'
            ],
            [
                'customer' => $customer,
            ])
                ->setTo($customer->email)
                ->setFrom($from)
                ->setBcc($bcc)
                ->setSubject($subject);
        if(! $msg->send())
        {
            Yii::error("mailer failed");
            return false;
        }
        return true;
    }
}
