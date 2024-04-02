<?php

namespace common\modules\invoice\components;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/modules/invoice/components/InvoiceMaker.php $
 * $Id: InvoiceMaker.php 4190 2019-10-03 08:41:56Z mori $
 */
use Yii;
use \common\models\Commission;
use \common\models\CustomerMembership;
use \common\models\Invoice;
use \common\models\Membership;
use \common\models\Payment;
use \common\models\Pointing;
use \common\models\Purchase;

class InvoiceMaker extends \yii\base\Component
{
    public function init()
    {
        parent::init();
    }

    public function generateAll($year, $month)
    {

        $customers = self::getCustomers($year, $month);
        if(empty($customers))
            return null; // no record found

        // メモリ制限、実行時間制限をカットする
        ini_set('memory_limit', '-1');
        ini_set("max_execution_time",0);

        foreach($customers as $customer_id)
            if(false === self::generateOne($year, $month, $customer_id))
                return false; // generate failed


        return true;
    }

    public function generateOne($year, $month, $customer_id)
    {
        // メモリ制限、実行時間制限をカットする
        ini_set('memory_limit', '-1');
        ini_set("max_execution_time",0);

        if(! self::validate($year, $month))
            throw new \yii\base\InvalidParamException();

        $invoice = Invoice::find()->where([
            'customer_id'    => $customer_id,
        ])->andWhere(['like',
                     'target_date', sprintf('%04d-%02d', $year, $month),
        ])->one();
        if(isset($invoice)) {
            $invoice->compute();
            if(0 == $invoice->due_total)
            {
                $invoice->delete();
                return true;
            }
        } else {
            $invoice = new Invoice([
                'customer_id'    => $customer_id,
                'target_date'    => sprintf('%04d-%02d', $year, $month),
            ]);
        }
        if (! $invoice->checkCompute())
          return true;

        $invoice->compute();
        // 支払日は、Invoice beforeSaveで下記で設定されていたので、ここでも使う。
        $invoice->due_date = date('Y-m-d', $invoice->getDueDate());

        if(! $invoice->validate() && $invoice->hasErrors('due_total'))
            return null; // the same data already exists

        // DirtyAttributesに、Modelで変更された内容が配列で格納される
        if($invoice->getDirtyAttributes())
        {
            // 変更がかかっているレコードだけSaveする
            if($invoice->save()){
                return true;
            }

            Yii::error(['Invoice::save() failed:',
                    'errors'     => $invoice->errors,
                    'attributes' => $invoice->attributes,
            ], self::className().'::'.__FUNCTION__);

            throw new \yii\base\Exception("エラーが発生しました。システム管理者にお問い合わせください。");

            return false;

        } else {
            return true;
        }
    }

    /* @return int customer_id[] */
    public function getCustomers($year, $month)
    {
        if(! self::validate($year, $month))
            throw new \yii\base\InvalidParamException();

        // skip if future
        if(time() < strtotime(sprintf('%04d-%02d', $year, $month)))
            return [];

        $purchases   = self::purchaseQuery(  $year, $month)->select('customer_id')->column();
        $pointings   = self::pointingQuery(  $year, $month)->select('seller_id'  )->column();
        $commissions = self::commissionQuery($year, $month)->select('customer_id')->column();
        $agencies    = self::agencyQuery(    $year, $month)->select('customer_id')->column();

        $customers = array_unique(array_merge($purchases, $pointings, $commissions));
        if(empty($customers))
            return [];

        $customers = array_unique(array_merge($customers, $agencies));

        return $customers;
    }

    private function agencyQuery($year, $month)
    {
        return CustomerMembership::find()
               ->active()
               ->distinct()
               ->andWhere(['membership_id' => [
                   Membership::PKEY_AGENCY_HE  ,
                   Membership::PKEY_AGENCY_HJ_A,
                   Membership::PKEY_AGENCY_HJ_B,
                   Membership::PKEY_AGENCY_HP  ,
               ]])
               ->andWhere(['>', 'expire_date', sprintf('%04d-%02d-00', $year, $month)]);
    }

    private function commissionQuery($year, $month)
    {
        return Commission::find()->where([
               'purchase_id' => 
                   Purchase::find()
                   ->active()
                   ->andWhere([
                       'EXTRACT(YEAR  FROM create_date)' => $year,
                       'EXTRACT(MONTH FROM create_date)' => $month,
                   ])
                   ->select('purchase_id')
                   ->column(),
        ]);
    }

    private function pointingQuery($year, $month)
    {
        return Pointing::find()
               ->active()
               ->andWhere([
                   'EXTRACT(YEAR  FROM create_date)' => $year,
                   'EXTRACT(MONTH FROM create_date)' => $month,
               ])
               ->andWhere('seller_id <> customer_id');
    }

    private function purchaseQuery($year, $month)
    {
        return Purchase::find()
               ->active()
               ->andWhere(['payment_id' => [Payment::PKEY_BANK_TRANSFER,
                                            Payment::PKEY_DIRECT_DEBIT  ]])
               ->andWhere([
                   'EXTRACT(YEAR  FROM create_date)' => $year,
                   'EXTRACT(MONTH FROM create_date)' => $month,
               ]);
    }

    private function validate($year, $month)
    {
        $model = new \common\models\DateForm([
            'year' => $year,
            'month'=> $month,
        ]);

        return ($year && $month && $model->validate());
    }

}
