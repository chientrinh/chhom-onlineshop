<?php

namespace backend\models;

/**
 * $URL$
 * $Id$
 */
use Yii;
use \yii\helpers\ArrayHelper;
use \common\models\Payment;
use \common\models\Purchase;
use \common\models\PurchaseForm;
use \common\models\PurchaseItem;
use \common\models\PurchaseStatus;

class RefundForm extends \yii\base\Model
{
    public $purchase_id;
    public $quantity;
    public $note;

    public function rules()
    {
        return [
            [['purchase_id','quantity','note'], 'required'],
            ['purchase_id', 'exist', 'targetClass'=>Purchase::className() ],
            ['purchase_id', 'validatePurchase'],
            ['quantity', 'each', 'rule' => ['integer','min'=>0], 'skipOnEmpty' => true],
            ['quantity', 'validateQuantity'],
            ['note',     'trim'],
            ['note',     'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'quantity' => '返品数',
            'note'     => '備考',
        ];
    }

    /**
     * 返品を起票する
     * @return (int) purchase_id of new record, or false when failed
     */
    public function save()
    {
        if(! $this->validate())
            return false;

        $tr = Yii::$app->db->beginTransaction();
        try
        {
            $base  = Purchase::findOne($this->purchase_id);
            $model = new PurchaseForm($base->getAttributes(null, ['purchase_id'/*except purchase_id*/]));

            $items = [];
            foreach($this->quantity as $seq => $qty)
            {
                if(0 == $qty) // 返品の対象ではない
                    continue;

                $q = $base->getItems()
                          ->where(['or', ['seq'=>$seq], ['parent' => $seq]]);

                foreach($q->all() as $row)
                {
                    $item = new PurchaseItem();
                    $item->load($row->attributes, '');
                    $item->purchase_id = $model->purchase_id;
                    $item->quantity    = $qty;
                    $items[] = $item;
                }
            }
            $model->items  = $items;
            if(1 == count($model->companies))
                $model->company_id = array_shift(array_values($model->companies))->company_id;

            $model->compute();
            $model->save();

            /* 数量や金額をすべて負数にする */
            // UPDATE dtb_purchase
            $model->db->createCommand()->update('dtb_purchase',[
                'subtotal'      => new \yii\db\Expression('0 - abs(subtotal)'),
                'tax'           => new \yii\db\Expression('0 - abs(tax)'),
                'total_charge'  => new \yii\db\Expression('0 - abs(subtotal) - abs(tax)'),
                'discount'      => 0,
                'receive'       => 0,
                'change'        => 0,
                'postage'       => 0,
                'handling'      => 0,
                'payment_id'    => in_array($model->payment_id, [Payment::PKEY_CASH,
                                                                 Payment::PKEY_NO_CHARGE])
                                   ? $model->payment_id : Payment::PKEY_BANK_TRANSFER /*銀行振込*/,
                'paid'          => in_array($model->payment_id, [Payment::PKEY_CASH,
                                                                 Payment::PKEY_NO_CHARGE])
                                   ? 1/*済み*/ : 0/*まだ*/,
                'shipped'       => 9, // 納品不要
                'point_consume' => 0,
                'point_given'   => new \yii\db\Expression('0 - abs(point_given)'),
		'status'        => PurchaseStatus::PKEY_RETURN,
                'note'          => "伝票: {$base->purchase_id} の返品を承りました",
                'customer_msg'  => $this->note,
            ], 'purchase_id = :pid', [':pid' => $model->purchase_id])
            ->execute();

            // UPDATE dtb_purchase_item
            $model->db->createCommand()->update('dtb_purchase_item',[
                'quantity' => new \yii\db\Expression('0 - abs(quantity)'),
            ], 'purchase_id = :pid', [':pid' => $model->purchase_id])
            ->execute();

            // UPDATE dtb_commission
            $model->db->createCommand()->update('dtb_commission',[
                'fee' => new \yii\db\Expression('0 - abs(fee)'),
            ], 'purchase_id = :pid', [':pid' => $model->purchase_id])
            ->execute();
        }
        catch (\yii\db\Exception $e)
        {
            Yii::error($e->__toString(), $this->className().'::'.__FUNCTION__);

                      $tr->rollBack();
            return false;
        }
        $tr->commit();

        return $model->purchase_id;
    }

    public function validatePurchase($attr, $param)
    {
        $q = Purchase::find()->andWhere(['<', 'total_charge', 0])
                             ->andWhere(['purchase_id' => $this->purchase_id]);
        if($q->exists())
            $this->addError($attr, "返品伝票を元にして返品を起票することはできません");

        return $this->hasErrors($attr);
    }

    public function validateQuantity($attr, $param)
    {
        if($this->hasErrors('purchase_id'))
            return false;

        $sum = 0;
        foreach($this->quantity as $seq => $qty)
        {
            $sum += $qty;

            if(! $item = PurchaseItem::findOne(['purchase_id' => $this->purchase_id,
                                                'seq'         => $seq]))
            {
                $this->addError($attr, "{$seq} は存在しません");
                break;
            }

            if($item->quantity < $qty)
                $this->addError($attr, "{$item->name}に対する返品数{$qty} は大きすぎます");
        }

        if(0 == $sum)
            $this->addError($attr, "返品数がいずれもゼロです");

        return $this->hasErrors($attr);
    }
}
