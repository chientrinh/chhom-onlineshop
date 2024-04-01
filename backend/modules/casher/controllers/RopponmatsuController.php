<?php

namespace backend\modules\casher\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/controllers/RopponmatsuController.php $
 * $Id: RopponmatsuController.php 3817 2018-01-11 08:48:26Z kawai $
 */

use Yii;
use \common\models\Company;
use \common\models\Payment;
use \common\models\PurchaseStatus;
use \common\models\Branch;

class RopponmatsuController extends WarehouseController
{
    public $branch_id = Branch::PKEY_ROPPONMATSU;

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
                 ->andWhere(['branch_id'  => Branch::PKEY_ROPPONMATSU]);

        $provider->pagination->pageSize = 20;

        return $this->render('index', [
            'dataProvider' => $provider,
            'searchModel'  => $model,
        ]);
    }

    /**
     * casher/transfer/create へリダイレクトする。このとき副作用として branch_id=六本松 が設定される
     * site/index に このページへリンクする Html::a() が配置されている 2016.11.13 mori
     */
    public function actionTransfer()
    {
        return $this->redirect(['transfer/create']);
    }

}
