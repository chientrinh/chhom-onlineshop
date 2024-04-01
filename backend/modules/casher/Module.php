<?php

namespace backend\modules\casher;

use Yii;
use \yii\helpers\Html;
use \yii\helpers\Url;
use \common\models\Branch;
use \common\models\CustomerGrade;
use \common\models\Payment;
use \common\models\PurchaseForm;
use \common\models\WtbPurchase;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/Module.php $
 * $Id: Module.php 4242 2020-03-20 05:15:48Z mori $
 */

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'backend\modules\casher\controllers';
    public $purchase;

    public function init()
    {
        parent::init();
    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        if(! $this->branch && ($user = Yii::$app->user->identity))
        {
            $q = $user->getRoles()->where(['not',['branch_id'=>0]]);

            if(1 == $q->count()) // このStaffは1拠点に専属である
            {
                $bid = $q->select('branch_id')->scalar();
                $this->setBranch($bid);
            }
        }

        if(in_array($action->controller->id,['default']) &&
            $this->branch &&
            $this->branch->isWarehouse() && ! in_array($action->id, ['apply'/*適用書をレジに追加*/,'setup']))
        {
            return Yii::$app->controller->redirect(['default/setup']);
        }

        if(in_array($action->id, ['create','duplicate','update','search','compose','apply','finish', 'commission-create', 'machine']) ||
           (('index' == $action->id) && ('transfer' == Yii::$app->controller->id))
        )
            $this->loadModel();

        if(isset($this->purchase))
            $this->purchase->validate();

        $this->initViewParams($action);

        return true;
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);

        if(in_array($action->id, ['create','duplicate','update','compose','apply','machine']))
            $this->savePointForm();

        if(in_array($action->id, ['create','duplicate','update','apply']))
            \backend\widgets\CartNav::register([
                'route'     => sprintf('/casher/%s/create', $action->controller->id),
                'itemCount' => $this->purchase->itemCount,
                'customer'  => $this->purchase->customer_id,
            ]);

        Url::remember(Url::current(), $this->id); // URL を記憶しておく

        return $result;
    }

    /* @return (Model | null) */
    public function getBranch()
    {
        if(Yii::$app->response->cookies->has('branch_id'))
            $branch_id = Yii::$app->response->cookies->getValue('branch_id');

        elseif(Yii::$app->request->cookies->has('branch_id'))
            $branch_id = Yii::$app->request->cookies->getValue('branch_id');

        if(! isset($branch_id))
            return null;

        return Branch::findOne($branch_id);
    }

    public function getPayments()
    {
        $pkey = [];
        $centers = [Branch::PKEY_HOMOEOPATHY_TOKYO,
                    Branch::PKEY_HOMOEOPATHY_SAPPORO,
                    Branch::PKEY_HOMOEOPATHY_NAGOYA,
                    Branch::PKEY_HOMOEOPATHY_OSAKA,
                    Branch::PKEY_HOMOEOPATHY_FUKUOKA];

        // レジの拠点がホメオパシーセンター５本部に該当すれば全部出し
        if(Branch::find()->center(true)->andWhere(['branch_id' => $this->branch->branch_id])->one()) {
            $pkey[] = Payment::PKEY_CASH;
            $pkey[] = Payment::PKEY_YAMATO_COD;
            $pkey[] = Payment::PKEY_DIRECT_DEBIT;
            $pkey[] = Payment::PKEY_BANK_TRANSFER;
            $pkey[] = Payment::PKEY_CREDIT_CARD;
        
            return Payment::find()->andWhere(['payment_id'=>$pkey])->asArray()->all();
        }

        if(! $this->branch->isWarehouse()) // 実店舗（熱海・六本松以外の場合）
        {
            $pkey[] = Payment::PKEY_CASH;
            $pkey[] = Payment::PKEY_CREDIT_CARD;
        }
        else
        {
            $pkey[] = Payment::PKEY_YAMATO_COD;
        }

        if($customer = $this->purchase->customer)
        {
            $agency = $customer->isAgency();

            // サポート申込した伝票であれば、支払い方法を指定なしで固定する
            if($this->purchase->agent_id) {
                $pkey = [Payment::PKEY_SUPPORT];
            } else {

                if((CustomerGrade::PKEY_AA <= $customer->grade_id || $agency) && isset($customer->ysdAccount) && $customer->ysdAccount->isValid())
                {
                    $pkey[] = Payment::PKEY_DIRECT_DEBIT;
                }
                else if($agency)
                {
                    $pkey[] = Payment::PKEY_BANK_TRANSFER;
                }

                // 熱海発送所かつ特定顧客のみ銀行振込に固定させる
                if($this->branch->branch_id == Branch::PKEY_ATAMI && in_array($customer->customer_id, [30500, 30501, 30513, 19172]))
                {
                    $pkey = [Payment::PKEY_BANK_TRANSFER];
                }
            }

        }
        return Payment::find()->andWhere(['payment_id'=>$pkey])->asArray()->all();
    }

    public function getPrintBasename()
    {
        return sprintf('%s_%s_%s',
                       date('Ymd'),
                       date('Hi'),
                       \common\components\Security::generateRandomString(4));
    }

    /* @return void */
    public function setBranch($id)
    {
        Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name'  => 'branch_id',
            'value' => $id,
            'path'  => \yii\helpers\Url::base(),
            'expire'=> time() + 3600 * 24, // 24 hour
        ]));
    }

    private function initViewParams($action)
    {
        $ctrl   = Yii::$app->controller;

        if(in_array($ctrl->id, ['atami','ropponmatsu']))
            $ctrl->view->params['breadcrumbs'][] = ['label'=>"発送所"];
        elseif('trose' == $ctrl->id)
            $ctrl->view->params['breadcrumbs'][] = ['label'=>"出店者"];
        else
            $ctrl->view->params['breadcrumbs'][] = ['label'=>"実店舗",'url'=>['default/setup']];
    }

    private function loadModel()
    {
        $this->purchase = new PurchaseForm([
            'payment_id' => \common\models\Payment::PKEY_CASH,
        ]);

        $buf = $this->loadBuffer();

        if($buf)
            $this->purchase->feed($buf);

        if(0 < $this->purchase->purchase_id)
        {
            $model = \common\models\PurchaseForm::findOne($this->purchase->purchase_id);
            if($model) // ! isNewRecord
            {
                // revert customer point
                if($model->customer_id && ($model->customer_id == $this->purchase->customer_id))
                    $this->purchase->customer->point += $model->point_consume;

                // swap the model (so that pkey can validate safely)
                $this->purchase = $model;
                $this->purchase->feed($buf); // update content
            }
        }

        if($this->purchase && $this->branch)
            $this->purchase->branch_id = $this->branch->branch_id;
    }

    public function reloadBuffer()
    {
        $prev_pt   = $this->purchase->point_consume;
        $prev_cstm = $this->purchase->customer;

        $buf       = $this->loadBuffer();
        
        if($buf && ($this->purchase->purchase_id == \yii\helpers\ArrayHelper::getValue($buf,'purchase_id')))
            $this->purchase->feed($buf);
    }

    private function savePointForm()
    {
        $buf = $this->purchase->dump();

        $this->saveBuffer($buf);
    }

    /* @return array */
    private function loadBuffer()
    {
        $sid = static::className() . Yii::$app->controller->id;
        return Yii::$app->session->get($sid, []);
    }

    /* @return void */
    private function saveBuffer($buf)
    {
        $sid = static::className() . Yii::$app->controller->id;
        Yii::$app->session->set($sid, $buf);
    }

    /* @return void */
    public function clearBuffer()
    {
        \backend\widgets\CartNav::release();

        $sid = static::className() . Yii::$app->controller->id;
        Yii::$app->session->remove($sid);

        $this->purchase = new PurchaseForm();
    }

}
