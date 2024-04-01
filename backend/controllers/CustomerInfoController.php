<?php

namespace backend\controllers;

use Yii;
use backend\models\CustomerInfo;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/CustomerInfoController.php $
 * $Id: CustomerInfoController.php 4007 2018-09-05 07:31:36Z mori $
 *
 * CustomerMembershipController implements the CRUD actions for CustomerMembership model.
 */
class CustomerInfoController extends BaseController
{
    /**
     * Lists all CustomerMembership models.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = CustomerInfo::find();
        $model = new CustomerInfo();

        if($model->load(Yii::$app->request->get()))
            foreach($model->dirtyAttributes as $attr => $value) {
                if ($attr == 'content') {
                    $query->andFilterWhere(['like', $attr, $value]);
                } else {
                    $query->andFilterWhere([$attr => $value]);
                }
            }

        $provider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index', [
            'dataProvider' => $provider,
            'searchModel'  => $model,
        ]);
    }

    /**
     * Creates a new CustomerMembership model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($customer_id)
    {
        $model = new CustomerInfo(['customer_id'=>$customer_id]);

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
        $model = CustomerInfo::findOne($id);

        if(! $model)
            throw new NotFoundHttpException('The requested page does not exist.');

        return $model;
    }
}
