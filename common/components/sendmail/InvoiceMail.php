<?php
namespace common\components\sendmail;

use Yii;
use \common\models\Invoice;
use \common\widgets\doc\invoice\InvoiceDocument;
use \backend\models\Staff;

class InvoiceMail extends \yii\base\Component
{
    public static function notify(Invoice $model)
    {
        if(! $model->validate())
            throw new \yii\base\Exception('you cannot send a invalid invoice');

        if($model->isPaid())
            throw new \yii\base\Exception('the customer has paid for this invoice');

        if(! $model->customer->email)
            throw new \yii\base\Exception('the customer does not have email');

        $bcc       = [Yii::$app->params['supportEmail']];
        if(($user  = Yii::$app->get('user')) && $user->identity instanceof Staff)
            $bcc[] = $user->identity->email;

        $subject = sprintf('[%s] お支払額のご案内 (%04d年%02d月度)', Yii::$app->name, $model->year, $model->month);

        $message = Yii::$app->mailer->compose(['text' => 'invoice-notify-text'],['model' => $model])
                         ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name ])
                         ->setTo($model->customer->email)
                         ->setBcc($bcc)
                         ->setSubject($subject);

        $widget = InvoiceDocument::begin(['model'=>$model]);
        $widget->renderPdf();

        $message->attach($widget->pdffile);

        $from   = [Yii::$app->params['supportEmail'] => Yii::$app->name ];
        $bcc    = Yii::$app->params['supportEmail'];

        $mailer = \Yii::$app->mailer;
        $mailer->table = $model->tableName();
        $mailer->pkey  = $model->invoice_id;

        if(! $message->send())
        {
            Yii::error("mailer failed");
            return false;
        }

        return true;
    }
}
