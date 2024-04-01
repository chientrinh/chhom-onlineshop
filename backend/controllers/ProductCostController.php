<?php

namespace backend\controllers;

use Yii;
use common\models\factory\ProductCost;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/ProductCostController.php $
 * $Id: ProductCostController.php 2307 2016-03-26 08:33:43Z mori $
 *
 * ProductCostController implements the CRUD actions for ProductCost model.
 * 
 */
class ProductCostController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['wizard'],
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label' => '商品',     'url' => ['/product/index']];
        $this->view->params['breadcrumbs'][] = ['label' => '製造原価', 'url' => ['index']];

        return true;
    }

    /**
     * Lists all ProductCost models.
     * @return mixed
     */
    public function actionIndex()
    {
        $provider = new ActiveDataProvider([
            'query' => ProductCost::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $provider,
        ]);
    }

    /**
     * Displays a single ProductCost model.
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
     * Creates a new ProductCost model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProductCost();

        if($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['view', 'id' => $model->cost_id]);

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProductCost model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['view', 'id' => $model->cost_id]);

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the ProductCost model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductCost the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if(! $model = ProductCost::findOne($id))
            throw new NotFoundHttpException('The requested page does not exist.');

        return $model;
    }
}
