<?php

namespace backend\controllers;

use Yii;
use common\models\ProductGrade;
use common\models\ProductGradeSearch;
use common\models\Streaming;
use common\models\StreamingBuy;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductGradeController implements the CRUD actions for ProductGrade model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/ProductGradeController.php $
 * $Id: ProductGradeController.php 2286 2020-04-28 16:11:00Z kawai $
 */
class ProductGradeController extends BaseController
{
    public function beforeAction($action)
    {
        if(! parent::beforeAction($action))
            return false;

        $this->view->params['breadcrumbs'][] = ['label'=>'会員ランク別商品価格','url'=>['index']];

        return true;
    }

    /**
     * Lists all ProductGrade models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductGradeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProductGrade model.
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
     * Creates a new ProductGrade model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProductGrade();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($model->save()) {
//                return $this->redirect(['view', 'id' => $model->product_grade_id]);
                Yii::$app->session->addFlash('success', 'ランク別価格レコードの作成に成功しました');
            } else {
                Yii::$app->session->addFlash('warning', print_r($model->errors, true));
            }
        } 
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProductGrade model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $param = Yii::$app->request->post();

        if($param)
        {

            foreach ($param['ProductGrade'] as $key => $value) {
                if($key == "grade_id" && $value == '0') {
                    $model->$key = null;
                } else {
                    $model->$key = $value;
                }
            }
            // $model->load($param);

        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }

        if ($model->validate() && $model->save(false)) {
            return $this->redirect(['view', 'id' => $model->product_grade_id]);
        } else {
            Yii::$app->session->addFlash('error', "更新時にエラーが発生しました。システム担当者へ連絡してください");
            var_dump($model->validate(), $model->errors);exit;

            return $this->render('update', ['model' => $model]);
        }
    }

    /**
     * Finds the ProductGrade model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductGrade the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductGrade::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
