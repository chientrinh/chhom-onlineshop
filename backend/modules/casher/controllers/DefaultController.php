<?php

namespace backend\modules\casher\controllers;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/controllers/DefaultController.php $
 * $Id: DefaultController.php 4161 2019-06-07 05:51:23Z mori $
 */

use Yii;
use \common\models\Branch;
use \common\models\Payment;
use \common\models\PurchaseDelivery;
use \backend\models\SearchPurchase;
use \backend\models\CsvUploadMultiForm;
use yii\web\UploadedFile;

class DefaultController extends BaseController
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        if($this->module->purchase)
        {
            // 実店舗は代引きを禁止する
//            if(! in_array($this->module->purchase->payment_id,[Payment::PKEY_CASH,
//                                                               Payment::PKEY_BANK_TRANSFER,
//                                                               Payment::PKEY_DIRECT_DEBIT,
//                                                               Payment::PKEY_CREDIT_CARD])
//            )
//                $this->module->purchase->payment_id = Payment::PKEY_CASH;

            // 日本ホメオパシーセンター５拠点だけは配達を可能とする
            if(Branch::find()->center(true)->andWhere(['branch_id' => $this->module->branch->branch_id])->one() || $this->module->branch->branch_id == 16) {
                if(! $this->module->purchase->delivery)
                {
                    if($this->module->branch->branch_id == 16) {
                        $delivery = $this->module->purchase->purchase_id ? \common\models\PurchaseDelivery::find()->where(['purchase_id' => $this->module->purchase->purchase_id])->one() : new \common\models\PurchaseDelivery();
                    } else {
                        $delivery = new \common\models\PurchaseDelivery();
        
                        if($customer = $this->module->purchase->customer)
                            foreach($customer->attributes as $attr => $val)
                                if($delivery->canSetProperty($attr))
                                    $delivery->$attr = $val;
                    }
                    $this->module->purchase->delivery = $delivery;
                    
                }
            } else {
                // 配達不可とする
                $this->module->purchase->delivery = null;
            }
        }

        return true;
    }

    public function actionCreate()
    {
        $this->view->params['breadcrumbs'][] = ['label'=> 'レジ'];

        return parent::actionCreate();
    }

    public function actionCommissionCreate()
    {
        $this->view->params['breadcrumbs'][] = ['label'=> '手数料管理'];

        if(isset($this->module->purchase->purchase_id) && 0 < $this->module->purchase->purchase_id) // now editing exsisting record
            return $this->redirect(['update', 'id'=>$this->module->purchase->purchase_id]);

        $this->module->purchase->validate();
        return $this->render('commission-create', ['model' => $this->module->purchase]);
    }

    public function actionIndex($params = null)
    {
        $csv_model = new CsvUploadMultiForm();

        if (Yii::$app->request->isPost) {
            $csv_model->csvFiles = UploadedFile::getInstances($csv_model, 'csvFiles');
            if ($csv_model->upload()) {
                // ファイルのアップロードが成功
                Yii::$app->session->addFlash('success',sprintf("%s件のファイルアップロードが完了しました",$csv_model->success_count));
                $result = "";
                $success_result = "";
                $error_result = "";
                $controller = new \console\controllers\PurchaseController(Yii::$app->controller->id, Yii::$app);

                foreach ($csv_model->csvFiles as $file) {
                    $result = $controller->actionBackendPurchaseImport(Yii::getAlias(sprintf('@runtime/%s.%s',$file->baseName, $file->extension)));
                    if(strpos($result,'エラー') !== false){
                        $error_result .= $result."<br />";
                    } else {
                        $success_result .= $result."<br />";
                    }
                }

                if(strlen($success_result) > 0)
                    Yii::$app->session->addFlash('success', sprintf("登録成功：<br />%s",$success_result));
                if(strlen($error_result) > 0)
                    Yii::$app->session->addFlash('error', sprintf("登録エラー：<br />%s",$error_result));

            } else {
                Yii::$app->session->addFlash('error',sprintf("%s件、不正なファイルが検出されました",$csv_model->error_count));
            }
        }
        if(!$params)
            $params = Yii::$app->request->get();

        $searchModel  = new SearchPurchase();
        $dataProvider = $searchModel->search($params);
        $dataProvider->pagination         = ['pageSize'=>50];
        $dataProvider->sort->defaultOrder = ['create_date' => SORT_DESC];

        if($branch = $this->module->branch)
            $dataProvider->query->andFilterWhere(['branch_id' => $branch->branch_id]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
            'csvModel'    => $csv_model,
        ]);
    }

    public function actionSetup($id=0, $create_date=null)
    {
        if($id)
        {
            $this->module->setBranch($id);

            if($url = \yii\helpers\Url::previous($this->id)) // URL を思い出す
                return $this->redirect($url);

            return $this->redirect(['index', 'SearchPurchase' => ['create_date' => $create_date]]);
        }

        if(($branch = $this->module->branch) &&
            $branch->isWarehouse()
        )
            $this->module->branch = null;

        return $this->render('setup');
    }


    /* @return void */
    public function applyCustomer($id)
    {
        parent::applyCustomer($id);

        // 日本ホメオパシーセンター５拠点だけは配達を可能とする
        if(!Branch::find()->center(true)->andWhere(['branch_id' => $this->module->branch->branch_id])->one()) {
            $this->module->purchase->delivery = null;
        } else {

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

}
