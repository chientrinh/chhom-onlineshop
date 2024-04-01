<?php

namespace backend\controllers;

use Yii;
use common\models\RemedyPriceRange;
use common\models\RemedyPriceRangeItem;
use common\models\SearchRemedyPriceRange;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RemedyPriceRangeController implements the CRUD actions for RemedyPriceRange model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/RemedyPriceRangeController.php $
 * $Id: RemedyPriceRangeController.php 1157 2015-07-15 13:01:02Z mori $
 */

class RemedyPriceRangeController extends BaseController
{

    /**
     * Lists all RemedyPriceRange models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchRemedyPriceRange();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RemedyPriceRange model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $item            = new \common\models\SearchRemedyPriceRangeItem();
        $item->prange_id = $id;
        $provider        = $item->search([]);

        return $this->render('view', [
            'model'   => $this->findModel($id),
            'provider'=> $provider,
        ]);
    }

    /**
     * Creates a new RemedyPriceRange model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RemedyPriceRange();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->prange_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing RemedyPriceRange model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->prange_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the RemedyPriceRange model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RemedyPriceRange the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RemedyPriceRange::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
