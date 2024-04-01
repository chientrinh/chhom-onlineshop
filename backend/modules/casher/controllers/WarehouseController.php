<?php

namespace backend\modules\casher\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/controllers/WarehouseController.php $
 * $Id: WarehouseController.php 2938 2016-10-08 08:40:12Z mori $
 */

use Yii;
use \common\models\PurchaseDelivery;

abstract class WarehouseController extends BaseController
{
    public $branch_id;

    public function init()
    {
        parent::init();

        $this->module->setBranch($this->branch_id);
    }

    public function getViewPath()
    {
        return dirname(__DIR__) . '/views/default';
    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        if($this->module->purchase)
        {
            if(! $this->module->purchase->delivery)
            {
                $delivery = new \common\models\PurchaseDelivery();

                if($customer = $this->module->purchase->customer)
                    foreach($customer->attributes as $attr => $val)
                        if($delivery->canSetProperty($attr))
                            $delivery->$attr = $val;

                $this->module->purchase->delivery = $delivery;
            }

            // 発送所では現金とクレジットカードを不可とする
            if(! $this->module->purchase->payment_id ||
               (in_array($this->module->purchase->payment_id, [\common\models\Payment::PKEY_CASH,
                                                               \common\models\Payment::PKEY_CREDIT_CARD]))
            )
                $this->module->purchase->payment_id = \common\models\Payment::PKEY_YAMATO_COD;
        }

        return true;
    }

    public function actionCreate()
    {
        $this->view->params['breadcrumbs'][] = ['label'=> '受注'];

        return parent::actionCreate();
    }

    /* @return void */
    public function applyCustomer($id)
    {
        parent::applyCustomer($id);

        if(! $customer = \common\models\Customer::findOne($id))
            return;

        $delivery = new PurchaseDelivery();
        if($customer)
        {
            $attrs = ['name01','name02','kana01','kana02','zip01','zip02','pref_id','addr01','addr02','tel01','tel02','tel03'];
            foreach($attrs as $attr) 
                $delivery->$attr = $customer->$attr;
        }

        $this->module->purchase->delivery = $delivery;
    }

}
