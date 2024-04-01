<?php

namespace backend\controllers;

use Yii;
use common\models\AgencyRating;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/AgencyRatingController.php $
 * $Id: AgencyRatingController.php 3106 2016-11-25 01:54:47Z mori $
 *
 * AgencyRatingController implements the CRUD actions for AgencyRating model.
 */
class AgencyRatingController extends Controller
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->initViewParams($action);

        return true;
    }

    /**
     * Lists all AgencyRating models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new AgencyRating();
        $model->load(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $this->loadProvider($model),
            'searchModel'  => $model,
        ]);
    }

    private function loadProvider($model)
    {
        $query = AgencyRating::find();
        $query->andFilterWhere([
            'customer_id'  => $model->customer_id,
            'company_id'   => $model->company_id,
            'discount_rate'=> $model->discount_rate,
            'start_date'   => $model->start_date,
            'end_date'     => $model->end_date,
        ]);

        $provider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $provider;
    }

    /**
     * Displays a single AgencyRating model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AgencyRating model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new AgencyRating(['customer_id' => $id,
                                   'company_id'  => \common\models\Company::PKEY_HE,
        ]);

        if($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['/customer/view', 'id' => $id]);

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AgencyRating model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['view', 'id' => $model->rating_id]);

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the AgencyRating model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AgencyRating the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if(! $model = AgencyRating::findOne($id))
            throw new NotFoundHttpException('The requested page does not exist.');

        return $model;
    }

    private function initViewParams($action)
    {
        $ctrl = Yii::$app->controller;

        $ctrl->view->params['breadcrumbs'][] = ['label' => '顧客', 'url' => ['/customer/index']];
        $ctrl->view->params['breadcrumbs'][] = ['label' => '代理店・割引率', 'url' => ['index']];

        if('create' == $action->id)
            $item = ['label' => "作成", 'url' => [$action->id]];
        if('update' == $action->id)
            $item = ['label' => "修正", 'url' => [$action->id]];

        if(isset($item))
            $ctrl->view->params['breadcrumbs'][] = $item;
    }

}
