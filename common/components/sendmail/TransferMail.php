<?php
namespace common\components\sendmail;

use Yii;
use \common\models\Branch;
use \backend\models\Staff;

class TransferMail extends \yii\base\Component
{
    /* Transfer model */
    public $model;

    /**
     * @return bool
     */
    public function thankyou()
    {
        $model   = $this->model;
        $subject = sprintf("%s 店間移動 [%06d]", Yii::$app->name, $model->purchase_id);
        $from    = $this->getFromAddr();
        $to      = $this->getToAddr();
        $cc      = $this->getCcAddr();

        $mailer = Yii::$app->mailer;
        $mailer->table = $model->tableName();
        $mailer->pkey  = $model->purchase_id;

        $msg = $mailer->compose(
            [
                'text'  => 'thankyou-transform-text'
            ],
            [
                'model'    => $model,
                'sender'   => array_shift(array_keys($from)),
            ])
                ->setSubject($subject)
                ->setCc($cc);

        if($from){ $msg->setFrom($from); }
        if($to)  { $msg->setTo($to);     }

        if($msg->send())
            return true;

        Yii::error("mailer failed for transfer: ".$model->purchase_id);
        return false;
    }

    private function getCcAddr()
    {
        if(! $s = Staff::findOne(Yii::$app->user->id))
            return null;

        return [$s->email => $s->name01];
    }

    private function getFromAddr()
    {
        if(! $b = Branch::findOne($this->model->src_id))
            return null;

        return [$b->email => $b->name];
    }

    private function getToAddr()
    {
        if(! $b = Branch::findOne($this->model->dst_id))
            return null;

        return [$b->email => $b->name];
    }
}
