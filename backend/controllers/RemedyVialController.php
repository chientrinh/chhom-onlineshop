<?php

namespace backend\controllers;

use Yii;
use common\models\RemedyVial;
use common\models\SearchRemedyVial;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RemedyVialController implements the CRUD actions for RemedyVial model.
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/controllers/RemedyVialController.php $
 * $Id: RemedyVialController.php 1157 2015-07-15 13:01:02Z mori $
 */
class RemedyVialController extends BaseController
{

    /**
     * Lists all RemedyVial models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchRemedyVial();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RemedyVial model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model'    => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new RemedyVial model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RemedyVial();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->vial_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing RemedyVial model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->vial_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the RemedyVial model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RemedyVial the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RemedyVial::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
