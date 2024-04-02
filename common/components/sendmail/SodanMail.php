<?php
namespace common\components\sendmail;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/sendmail/SodanMail.php $
 * $Id: SodanMail.php 1778 2015-11-09 05:04:09Z mori $
 */

use Yii;

class SodanMail extends \yii\base\Component
{
    public static function canceled(\common\models\sodan\Room $model)
    {
        $subject = "健康相談キャンセルのお知らせ";
        $from    = [Yii::$app->params['supportEmail'] => Yii::$app->name ];
        $bcc     = Yii::$app->params['supportEmail'];
        $user    = Yii::$app->user->identity;

        $mailer        = Yii::$app->mailer;
        $mailer->table = $model->tableName();
        $mailer->pkey  = $model->room_id;

        if((! $hpath = $model->homoeopath) ||
           (! $hpath->validate('email'))
        )
            return false;

        $msg = $mailer->compose(
            [
                'text'  => 'sodan-canceled-text'
            ],
            [
                'model'  => $model,
            ])
             ->setTo($hpath->email)
             ->setFrom($from)
             ->setCc($user->email)
             ->setSubject($subject);

        if(! $msg->send())
            return false;

        return true;
    }
}
