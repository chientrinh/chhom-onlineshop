<?php

namespace backend\modules\casher\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/controllers/AtamiController.php $
 * $Id: AtamiController.php 3518 2017-07-27 01:26:26Z kawai $
 */

use Yii;
use \common\models\Branch;
use \common\models\Company;
use \common\models\Payment;
use \common\models\PurchaseStatus;

class AtamiController extends WarehouseController
{
    public $branch_id = Branch::PKEY_ATAMI;

    public function actionIndex()
    {
        $model    = new \backend\models\SearchPurchase(['status' => PurchaseStatus::PKEY_INIT]);
        $model->load(Yii::$app->request->get());

        if(in_array($model->payment_id, ['yes','no']))
        {
            $handling = $model->payment_id; // 本当は payment.handling を参照したかった
            $model->payment_id = null;

            $bool = ($handling == 'yes');
        }

        $provider = $model->search();

        if(isset($bool))
        {
            $provider->query->andWhere(['payment_id' => Payment::find()->where(['handling'=> $bool ])->column()]);
            $model->payment_id = $bool ? 'yes' : 'no';
        }

        $provider->query
                 ->andWhere(['branch_id'  => [Branch::PKEY_ATAMI]]);
        $provider->pagination->pageSize = 50;

        return $this->render('index', [
            'dataProvider' => $provider,
            'searchModel'  => $model,
        ]);
    }

}
