<?php

namespace backend\controllers;

use Yii;
use common\models\CustomerAgencyRank;
use yii\web\NotFoundHttpException;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/CustomerAgencyRankController.php $
 * $Id: CustomerAgencyRankController.php 2269 2016-03-19 06:24:05Z mori $
 *
 * CustomerAgencyRankController implements the CRUD actions for CustomerAgencyRank model.
 */
class CustomerAgencyRankController extends BaseController
{

    /**
     * Creates a new CustomerMembership model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($customer_id)
    {
        $model = new CustomerAgencyRank([
            'customer_id' => $customer_id,
            'start_date'  => date('Y-m-d'),
            'expire_date' => '3000-12-31',
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect([
                '/customer/view',
                'id' => $model->customer_id,
            ]);

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CustomerMembership model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $customer_id
     * @param integer $membership_id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect([
                '/customer/view',
                'id'   => $model->customer_id,
            ]);

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the CustomerMembership model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $customer_id
     * @param integer $membership_id
     * @return CustomerMembership the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if($model = CustomerAgencyRank::findOne($id))
            return $model;

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
