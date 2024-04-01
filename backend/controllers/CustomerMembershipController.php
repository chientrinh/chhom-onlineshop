<?php

namespace backend\controllers;

use Yii;
use backend\models\CustomerMembershipForm;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/CustomerMembershipController.php $
 * $Id: CustomerMembershipController.php 2269 2016-03-19 06:24:05Z mori $
 *
 * CustomerMembershipController implements the CRUD actions for CustomerMembership model.
 */
class CustomerMembershipController extends BaseController
{

    /**
     * Creates a new CustomerMembership model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($customer_id)
    {
        $model = new CustomerMembershipForm([
            'customer_id'=> $customer_id,
            'start_date' => date('Y-m-d'),
            'expire_date'=> '3000-12-31',
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect([
                '/customer/view',
                'id'   => $model->customer_id,
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
        if($model = CustomerMembershipForm::findOne($id))
            return $model;

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
