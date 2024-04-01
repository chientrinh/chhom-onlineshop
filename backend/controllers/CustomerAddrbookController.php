<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/CustomerAddrbookController.php $
 * $Id: CustomerAddrbookController.php 2995 2016-10-20 05:31:57Z mori $
 */

namespace backend\controllers;

use Yii;
use common\models\Customer;
use common\models\CustomerAddrbook;
use common\models\SearchCustomerAddrbook;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CustomerAddrbookController implements the CRUD actions for CustomerAddrbook model.
 */
class CustomerAddrbookController extends BaseController
{
    /**
     * Lists all CustomerAddrbook models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchCustomerAddrbook();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CustomerAddrbook model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $customer = Customer::findOne($id);

        return $this->render('view', [
            'customer' => $customer,
        ]);
    }

    /**
     * Creates a new CustomerAddrbook model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new CustomerAddrbook(['customer_id' => $id]);

        if($model->load(Yii::$app->request->post()))
        {
            if('zip2addr' == Yii::$app->request->post('scenario'))
            {
                if($model->zip2addr())
                    Yii::$app->session->addFlash('success','住所を更新しました');
                else
                    Yii::$app->session->addFlash('warning','住所は更新されませんでした');
            }
            elseif($model->save())
                return $this->redirect(['view', 'id' => $model->customer_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CustomerAddrbook model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->load(Yii::$app->request->post()))
        {
            if('zip2addr' == Yii::$app->request->post('scenario'))
            {
                if($model->zip2addr())
                    Yii::$app->session->addFlash('success','住所を更新しました');
                else
                    Yii::$app->session->addFlash('warning','住所は更新されませんでした');
            }

            elseif($model->save())
                return $this->redirect(['view', 'id' => $model->customer_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the CustomerAddrbook model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CustomerAddrbook the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomerAddrbook::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('要求されたIDがみつかりません');
        }
    }
}
