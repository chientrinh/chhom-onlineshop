<?php

namespace backend\modules\casher\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/controllers/TroseController.php $
 * $Id: TroseController.php 3212 2017-03-05 06:56:30Z naito $
 */

use Yii;
use \common\models\Company;
use \common\models\PurchaseStatus;

class TroseController extends WarehouseController
{
    public $branch_id = \common\models\Branch::PKEY_TROSE;

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        if($this->module->purchase)
            $this->module->purchase->payment_id = \common\models\Payment::PKEY_POSTAL_COD;

        return true;
    }

    public function actionIndex()
    {
//      $model    = new \backend\models\SearchPurchase(['shipped' => 0]);
        $model    = new \backend\models\SearchPurchase(['status' => PurchaseStatus::PKEY_INIT]);
        $provider = $model->search(Yii::$app->request->get());

        $provider->query
                 ->andWhere(['company_id'  => Company::PKEY_TROSE]);

        $provider->pagination->pageSize = 20;

        return $this->render('index', [
            'dataProvider' => $provider,
            'searchModel'  => $model,
        ]);
    }

}
