<?php
namespace common\modules\member\models;

/**
 * This is the extended model class for table "{{%dtb_product}}".
 *
 * $URL: https://localhost:44344/svn/MALL/common/models/Product.php $
 * $Id: Product.php 1804 2015-11-13 17:22:28Z mori $
 */

use \common\models\Membership;

class Oasis extends \common\models\Product
{
    public $create_date;

    public static function find()
    {
        return parent::find()->oasis();
    }

    public function getCustomers($shipped = null)
    {
        if($shipped)
            return $this->getPurchases();

        $start = $this->start_date;
        $exp   = $this->expire_date;
        $query = \common\models\Customer::find();
        $q2    = \common\models\CustomerMembership::find();
        $q2->toranoko()
           ->andWhere(['not',['membership_id'=>[
               Membership::PKEY_TORANOKO_NETWORK,
               Membership::PKEY_TORANOKO_NETWORK_UK,
           ]]])
           ->andWhere(['not',['>','start_date', $exp  ]])
           ->andWhere(['not',['<','expire_date',$start]]);

        $query->child(false)
              ->andWhere(['customer_id' => $q2->select('customer_id')]);

        if(null !== $shipped)
            $query->andWhere(['not', [
                'dtb_customer.customer_id' => parent::getCustomers()->select('customer_id')
            ]]);

        return $query;
    }

    public function getPurchase(\common\models\Customer $customer)
    {
        $q1  = \common\models\Purchase::find()->active();
        $q2  = \common\models\Pointing::find()->active();
        $pid = $this->product_id;

        $q1 = $q1->select(['customer_id','dtb_purchase.create_date','dtb_purchase.purchase_id','(select NULL) as pointing_id'])
                 ->andWhere(['customer_id' => $customer->customer_id])
                 ->joinWith([
                     'items'=> function($query) use($pid) { $query->andWhere(['product_id' => $pid]); }
                 ])->asArray();

        $q2 = $q2->select(['customer_id','dtb_pointing.create_date','(select NULL) as purchase_id','dtb_pointing.pointing_id'])
                 ->andWhere(['customer_id' => $customer->customer_id])
                 ->joinWith([
                     'items'=> function($query) use($pid) { $query->andWhere(['product_id' => $pid]); }
                 ])->asArray();

        $q1->union($q2);

        return $q1;
    }

    public function getPurchases()
    {
        $q1  = \common\models\Purchase::find()->active();
        $q2  = \common\models\Pointing::find()->active();
        $pid = $this->product_id;

        $q1->select(['customer_id','dtb_purchase.create_date','dtb_purchase.purchase_id','(select NULL) as pointing_id'])
           ->joinWith([
               'items'=> function($query) use($pid) { $query->andWhere(['product_id' => $pid]); }
           ])->asArray();

        $q2->select(['customer_id','dtb_pointing.create_date','(select NULL) as purchase_id','dtb_pointing.pointing_id'])
           ->joinWith([
               'items'=> function($query) use($pid) { $query->andWhere(['product_id' => $pid]); }
           ])->asArray();

        return $q1->union($q2);
    }

    public function setPointing(\common\models\Customer $customer, \common\models\Customer $user)
    {
        if($this->getPurchase($customer)->one())
            throw new \yii\base\UserException('当該商品は顧客へ送付済みです');

        $model = new \common\models\PointingForm([
            'company_id'  => \common\models\Company::PKEY_HE,
            'seller_id'   => $user->id,
            'customer_id' => $customer->customer_id,
        ]);
        $model->addItem($this);
        $model->items[0]->price      = 0;
        $model->items[0]->point_rate = 0;
        $model->compute(false);

        if(! $model->save())
            throw new \yii\base\UserException('すみません、エラーが発生しました');

        return $model;
    }

    public function setPurchase(\common\models\Customer $customer)
    {
        if($this->getPurchase($customer)->one())
            throw new \yii\base\UserException('当該商品は顧客へ送付済みです');

        $model = new \common\models\PurchaseForm([
            'company_id'  => \common\models\Company::PKEY_HE,
            'branch_id'   => \common\models\Branch::PKEY_HE_TORANOKO,
            'customer_id' => $customer->customer_id,
            'payment_id'  => \common\models\Payment::PKEY_NO_CHARGE,
            'paid'        => 1,
            'shipped'     => 1,
            'status'      => \common\models\PurchaseStatus::PKEY_DONE,
        ]);
        $item = new \common\models\PurchaseItem(['company_id' => $this->category->seller_id]);

        foreach($item->attributes as $name => $value)
        {
            if($this->hasAttribute($name))
                $item->$name = $this->$name;
        }
        $item->quantity      =   1;
        $item->discount_rate = 100;
        $item->point_rate    =   0;

        $model->items[] = $item;
        $model->compute(false);

        if(! $model->save())
            throw new \yii\base\UserException('すみません、エラーが発生しました');

        return $model;
    }

}
